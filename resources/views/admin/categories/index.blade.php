@extends('layouts.admin')
@section('title', 'Kategorie')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-zinc-100">Kategorie</h1>
        <p class="mt-1 text-sm text-zinc-500">Kategoryzacja produktów w sklepie.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-yellow-400 px-4 py-2 text-sm font-semibold text-black hover:bg-yellow-300 transition-colors">
        <i data-lucide="plus" class="h-4 w-4"></i>
        Dodaj kategorię
    </a>
</div>

<div class="rounded-lg border border-zinc-800 bg-zinc-900">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-zinc-800 text-left text-xs uppercase text-zinc-500">
                    <th class="px-5 py-3 font-medium">Nazwa</th>
                    <th class="px-5 py-3 font-medium">Slug</th>
                    <th class="px-5 py-3 font-medium">Produkty</th>
                    <th class="px-5 py-3 text-right font-medium">Akcje</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-800">
                @forelse($categories as $category)
                    <tr class="hover:bg-zinc-800/50">
                        <td class="px-5 py-3">
                            <span class="font-medium text-zinc-200">{{ $category->name }}</span>
                            @if($category->description)
                                <p class="mt-0.5 text-xs text-zinc-500">{{ Str::limit($category->description, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-zinc-400">{{ $category->slug }}</td>
                        <td class="px-5 py-3 text-zinc-200">{{ $category->products_count }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center gap-1 text-sm text-yellow-400 hover:text-yellow-300 transition-colors">
                                Edytuj
                                <i data-lucide="arrow-right" class="h-3 w-3"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center text-sm text-zinc-500">
                            Brak kategorii. <a href="{{ route('admin.categories.create') }}" class="text-yellow-400 hover:underline">Dodaj pierwszą</a>.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
        <div class="border-t border-zinc-800 px-5 py-3">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection
