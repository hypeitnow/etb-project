@extends('layouts.app')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    @include('pages.partials.static-content-section', [
        'sectionId' => 'third-league',
        'eyebrow' => 'Rozgrywki',
        'title' => 'III liga mężczyzn ŁZKosz',
        'description' => 'Sekcja z informacją o rozgrywkach ligowych i przejściem do oficjalnej strony ŁZKosz.',
        'panelText' => 'Po uzupełnieniu treści można tu dodać opis ligi, najważniejsze komunikaty i materiały dla kibiców.',
        'actionUrl' => 'https://www.lzkosz.pl/liga/215.html',
        'actionLabel' => 'Otwórz stronę ŁZKosz',
    ])
</section>
@endsection
