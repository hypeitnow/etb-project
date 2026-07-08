<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ShippingProviderInterface;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceService;
use App\Services\OrderNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderNotificationService $notifications,
        private readonly ShippingProviderInterface $shipping,
        private readonly InvoiceService $invoices,
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('id', $search)
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.orders.index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load(['user', 'items.product', 'items.variantSize', 'statusLogs.user', 'invoice']);

        return view('admin.orders.show', compact('order'));
    }

    public function transition(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', $order->availableTransitions())],
            'note' => ['nullable', 'string', 'max:1000'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $fromStatus = $order->status;
            $order->transitionTo(
                $validated['status'],
                $request->user(),
                $validated['note'] ?? null,
            );

            if (! empty($validated['tracking_number'])) {
                $order->update(['tracking_number' => $validated['tracking_number']]);
            }

            $this->notifications->notifyStatusChange($order, $fromStatus, $validated['status'], $validated['note'] ?? null);
        } catch (\InvalidArgumentException) {
            return redirect()
                ->back()
                ->with('error', 'Nie można wykonać tego przejścia statusu.');
        }

        return redirect()
            ->back()
            ->with('success', 'Status zamówienia został zaktualizowany.');
    }

    public function downloadInvoice(Order $order): RedirectResponse|StreamedResponse
    {
        $invoice = $order->invoice;

        if (! $invoice || ! $invoice->pdf_path || ! Storage::disk('local')->exists($invoice->pdf_path)) {
            return redirect()->back()->with('error', 'Brak faktury dla tego zamówienia.');
        }

        return Storage::disk('local')->download($invoice->pdf_path, sprintf('faktura_%s.pdf', str_replace('/', '-', $invoice->number)));
    }

    public function generateLabel(Order $order): RedirectResponse
    {
        try {
            $labelPath = $this->shipping->generateLabel($order);
            Log::info('Label generated for order', ['order_id' => $order->id, 'path' => $labelPath]);

            return redirect()->back()->with('success', 'Etykieta została wygenerowana.');
        } catch (\Exception $e) {
            Log::error('Label generation failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Nie udało się wygenerować etykiety.');
        }
    }
}
