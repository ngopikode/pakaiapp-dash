@php
    use App\Tenant\Models\Core\Category;
    use App\Tenant\Models\Core\Product;

    $categories = Category::orderBy('order_column')->pluck('name')->toArray();
    $hasPromoItems = Product::whereHas('variants', fn($q) => $q->whereNotNull('active_discount_price'))->exists();
@endphp

<x-layouts::store>
    <div x-data="{
        category: new URLSearchParams(location.search).get('kategori') || 'all',
        search: new URLSearchParams(location.search).get('q') || '',
        sort: new URLSearchParams(location.search).get('sort') || 'popular',
        minPrice: new URLSearchParams(location.search).get('min') || null,
        maxPrice: new URLSearchParams(location.search).get('max') || null,
        viewMode: 'grid',
        showFilter: false,
        apply() {
            $dispatch('update-filters', {
                category: this.category,
                search: this.search,
                sort: this.sort,
                minPrice: this.minPrice,
                maxPrice: this.maxPrice
            });
        },
        reset() {
            this.sort = 'popular';
            this.minPrice = null;
            this.maxPrice = null;
            this.apply();
            this.showFilter = false;
        }
    }"
    @refresh-menu-data.window="apply()"
    x-init="$watch('viewMode', val => $dispatch('view-mode-changed', val))">
        @include('pages.tenant.store.resto.partials._filter-bar', [
            'categories' => $categories,
            'hasPromoItems' => $hasPromoItems
        ])
        <livewire:pages::tenant.store.resto.product-grid/>
    </div>
</x-layouts::store>
