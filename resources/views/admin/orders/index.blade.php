<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Zamówienia
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm" onchange="this.form.submit()">
                                    <option value="">Wszystkie</option>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-700 mb-1">Szukaj</label>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="ID zamówienia lub klient..." class="border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">Szukaj</button>
                            @if(request()->has('status') || request()->has('search'))
                                <a href="{{ route('admin.orders.index') }}" class="text-gray-500 text-sm hover:underline px-2 py-2">Wyczyść</a>
                            @endif
                        </form>
                    </div>

                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Klient</th>
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Kwota</th>
                                <th class="px-4 py-3 text-right">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium">#{{ $order->id }}</td>
                                    <td class="px-4 py-3">{{ $order->user->name }}</td>
                                    <td class="px-4 py-3">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="px-4 py-3">
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
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium">{{ $order->displayTotal() }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:underline text-sm">Szczegóły</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">Brak zamówień</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $orders->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
