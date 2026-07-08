<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
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
        $order->load(['user', 'items.product', 'items.variantSize', 'statusLogs.user']);

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
            $order->transitionTo(
                $validated['status'],
                $request->user(),
                $validated['note'] ?? null,
            );

            if (! empty($validated['tracking_number'])) {
                $order->update(['tracking_number' => $validated['tracking_number']]);
            }
        } catch (\InvalidArgumentException) {
            return redirect()
                ->back()
                ->with('error', 'Nie można wykonać tego przejścia statusu.');
        }

        return redirect()
            ->back()
            ->with('success', 'Status zamówienia został zaktualizowany.');
    }
}
