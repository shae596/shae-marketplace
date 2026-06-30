@php
    $activeCategoryId = (int) request('category_id');
    $icons = [
        'Électronique' => '📱',
        'Mode' => '👗',
        'Maison' => '🏠',
        'Alimentation' => '🍳',
        'Beauté' => '💄',
    ];
@endphp
@if(isset($navCategories) && $navCategories->count())
<nav class="shae-category-bar">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center gap-2 py-1">
            <a href="{{ route('home') }}" class="shae-cat-pill {{ !$activeCategoryId ? 'active' : '' }}">Accueil</a>
            @foreach($navCategories as $category)
                <a href="{{ route('home', ['category_id' => $category->id]) }}"
                   class="shae-cat-pill {{ $activeCategoryId === $category->id ? 'active' : '' }}">
                    {{ ($icons[$category->name] ?? '🏷️').' '.$category->name }}
                </a>
            @endforeach
        </div>
    </div>
</nav>
@endif
