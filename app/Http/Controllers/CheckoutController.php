<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Cart;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cart,
        private readonly PaymentGatewayInterface $gateway,
    ) {}

    public function shipping(): View|RedirectResponse
    {
        if ($this->cart->getItems(auth()->user())->isEmpty()) {
            return redirect()->route('shop.index')->with('error', __('Koszyk jest pusty.'));
        }

        $items = $this->cart->getItems(auth()->user());
        $totalGrosze = $this->cart->totalGrosze(auth()->user());
        $shippingGrosze = $this->calculateShipping($items);
        $needsShipping = $this->requiresShipping($items);

        return view('checkout.shipping', compact('items', 'totalGrosze', 'shippingGrosze', 'needsShipping'));
    }

    public function storeShipping(Request $request): RedirectResponse
    {
        $items = $this->cart->getItems(auth()->user());
        $needsShipping = $this->requiresShipping($items);

        $rules = [];
        if ($needsShipping) {
            $rules = [
                'shipping_method' => ['required', 'string', 'in:courier,inpost,pickup'],
                'address.street' => ['required', 'string', 'max:255'],
                'address.city' => ['required', 'string', 'max:255'],
                'address.postal_code' => ['required', 'string', 'max:20'],
                'address.country' => ['required', 'string', 'max:100'],
            ];
        }

        $validated = $request->validate($rules);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        $cart->shipping_method = $needsShipping ? $validated['shipping_method'] : null;
        $cart->shipping_address = $needsShipping ? $validated['address'] : null;
        $cart->save();

        return redirect()->route('checkout.payment');
    }

    public function payment(): View|RedirectResponse
    {
        if ($this->cart->getItems(auth()->user())->isEmpty()) {
            return redirect()->route('shop.index')->with('error', __('Koszyk jest pusty.'));
        }

        $items = $this->cart->getItems(auth()->user());
        $totalGrosze = $this->cart->totalGrosze(auth()->user());
        $cart = Cart::where('user_id', auth()->id())->first();
        $shippingGrosze = $cart ? $this->calculateShipping($items) : 0;

        return view('checkout.payment', compact('items', 'totalGrosze', 'shippingGrosze', 'cart'));
    }

    public function place(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $items = $this->cart->getItems($user);

        if ($items->isEmpty()) {
            return redirect()->route('shop.index')->with('error', __('Koszyk jest pusty.'));
        }

        $cart = Cart::where('user_id', $user->id)->first();
        $totalGrosze = $this->cart->totalGrosze($user);
        $shippingGrosze = $this->calculateShipping($items);

        $idempotencyKey = (string) str()->uuid();

        try {
            $order = DB::transaction(function () use ($user, $items, $cart, $totalGrosze, $shippingGrosze, $idempotencyKey) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'status' => Order::STATUS_PENDING_PAYMENT,
                    'total_grosze' => $totalGrosze,
                    'shipping_grosze' => $shippingGrosze,
                    'shipping_method' => $cart?->shipping_method,
                    'shipping_address' => $cart?->shipping_address,
                    'idempotency_key' => $idempotencyKey,
                ]);

                foreach ($items as $item) {
                    $order->items()->create([
                        'product_id' => $item->product->id,
                        'variant_size_id' => $item->variant?->id,
                        'qty' => $item->qty,
                        'unit_price_grosze' => $item->unit_price_grosze,
                    ]);
                }

                return $order;
            });

            $token = $this->gateway->createPayment($order);

            $this->cart->clear($user);

            $paymentUrl = config('przelewy24.base_url', 'https://sandbox.przelewy24.pl').
                '/trnRequest/'.$token;

            return redirect()->away($paymentUrl);
        } catch (\Exception $e) {
            Log::error('Order placement failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->route('checkout.payment')
                ->with('error', __('Nie udało się złożyć zamówienia. Spróbuj ponownie.'));
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $data = $request->all();

        try {
            $verified = $this->gateway->verifyTransaction($data);

            if (! $verified) {
                Log::warning('P24 webhook: invalid signature', $data);

                return response()->json(['status' => 'error'], 400);
            }

            $sessionId = $data['sessionId'];
            $orderId = (int) explode('|', $sessionId)[0];
            $order = Order::findOrFail($orderId);

            if ($order->isPaid()) {
                return response()->json(['status' => 'already_paid']);
            }

            DB::transaction(function () use ($order) {
                $order->update([
                    'status' => Order::STATUS_PAID,
                    'paid_at' => now(),
                ]);
            });

            Log::info("Order #{$order->id} paid via P24");

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('P24 webhook error', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    public function confirmation(Order $order): View|RedirectResponse
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product', 'items.variantSize');

        return view('checkout.confirmation', compact('order'));
    }

    private function requiresShipping($items): bool
    {
        return $items->contains(fn ($item) => $item->product?->is_physical);
    }

    private function calculateShipping($items): int
    {
        if (! $this->requiresShipping($items)) {
            return 0;
        }

        $cart = Cart::where('user_id', auth()->id())->first();
        $method = $cart?->shipping_method;

        return match ($method) {
            'courier' => 1500,
            'inpost' => 1200,
            'pickup' => 0,
            default => 1500,
        };
    }
}
