@php
    use App\Tenant\Models\Core\Category;
    use App\Tenant\Models\Core\Product;

    $categories = Category::orderBy('order_column')->pluck('name')->toArray();
    $hasPromoItems = Product::whereHas('variants', fn($q) => $q->whereNotNull('active_discount_price'))->exists();
@endphp

<x-layouts::store>
    @include('pages.tenant.store.resto.partials._filter-bar', [
        'categories' => $categories,
        'hasPromoItems' => $hasPromoItems
    ])
    <livewire:pages::tenant.store.resto.product-grid/>
</x-layouts::store>
