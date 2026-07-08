<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Zamówienie #{{ $order->id }}
            </h2>
            <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:underline text-sm">&larr; Powrót do listy</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Klient</p>
                            <p class="font-medium mt-1">{{ $order->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Status</p>
                            <p class="mt-1">
                                @php $colors = [
                                    'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                    'paid' => 'bg-green-100 text-green-800',
                                    'shipped' => 'bg-blue-100 text-blue-800',
                                    'delivered' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                ]; @endphp
                                <span class="px-2 py-1 rounded text-xs font-semibold {{ $colors[$order->status] ?? 'bg-gray-100' }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wide">Data zamówienia</p>
                            <p class="font-medium mt-1">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        @if($order->paid_at)
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Data płatności</p>
                                <p class="font-medium mt-1">{{ $order->paid_at->format('d.m.Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Pozycje zamówienia</h3>
                    <table class="w-full text-sm">
                        <thead class="text-gray-500 uppercase text-xs border-b">
                            <tr>
                                <th class="pb-2 text-left">Produkt</th>
                                <th class="pb-2 text-left">Rozmiar</th>
                                <th class="pb-2 text-right">Cena jedn.</th>
                                <th class="pb-2 text-right">Ilość</th>
                                <th class="pb-2 text-right">Suma</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="py-3">{{ $item->product->name ?? '[usunięty]' }}</td>
                                    <td class="py-3 text-gray-600">{{ $item->variantSize->size_label ?? '—' }}</td>
                                    <td class="py-3 text-right">{{ number_format($item->unit_price_grosze / 100, 2, ',', '') }} zł</td>
                                    <td class="py-3 text-right">{{ $item->qty }}</td>
                                    <td class="py-3 text-right font-medium">{{ number_format($item->subtotal() / 100, 2, ',', '') }} zł</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="text-sm font-medium border-t">
                            <tr>
                                <td colspan="3" class="pt-3 text-right">Produkty:</td>
                                <td class="pt-3 text-right">{{ number_format($order->total_grosze / 100, 2, ',', '') }} zł</td>
                            </tr>
                            @if($order->shipping_grosze > 0)
                                <tr>
                                    <td colspan="3" class="pt-1 text-right">Dostawa ({{ $order->shipping_method ?? '—' }}):</td>
                                    <td class="pt-1 text-right">{{ number_format($order->shipping_grosze / 100, 2, ',', '') }} zł</td>
                                </tr>
                            @endif
                            <tr class="text-base">
                                <td colspan="3" class="pt-2 text-right">Razem:</td>
                                <td class="pt-2 text-right">{{ $order->displayTotal() }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->shipping_address)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-semibold mb-3">Adres dostawy</h3>
                        <p>{{ $order->shipping_address['street'] ?? '' }}</p>
                        <p>{{ $order->shipping_address['postal_code'] ?? '' }} {{ $order->shipping_address['city'] ?? '' }}</p>
                        <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                        @if($order->shipping_method)
                            <p class="mt-2 text-sm text-gray-600">Metoda: {{ $order->shipping_method }}</p>
                        @endif
                        @if($order->tracking_number)
                            <p class="mt-1 text-sm text-gray-600">Numer przesyłki: {{ $order->tracking_number }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-3">Informacje techniczne</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div class="flex gap-2">
                            <dt class="text-gray-500">ID:</dt>
                            <dd class="font-mono">{{ $order->id }}</dd>
                        </div>
                        @if($order->payment_session_id)
                            <div class="flex gap-2">
                                <dt class="text-gray-500">Session ID:</dt>
                                <dd class="font-mono text-xs break-all">{{ $order->payment_session_id }}</dd>
                            </div>
                        @endif
                        @if($order->idempotency_key)
                            <div class="flex gap-2">
                                <dt class="text-gray-500">Idempotency Key:</dt>
                                <dd class="font-mono text-xs break-all">{{ $order->idempotency_key }}</dd>
                            </div>
                        @endif
                        <div class="flex gap-2">
                            <dt class="text-gray-500">Utworzono:</dt>
                            <dd>{{ $order->created_at->format('d.m.Y H:i:s') }}</dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="text-gray-500">Zaktualizowano:</dt>
                            <dd>{{ $order->updated_at->format('d.m.Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
