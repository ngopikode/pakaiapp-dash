<?php

use App\Tenant\Models\Core\Category;
use App\Tenant\Models\Core\Product;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component {
    #[Url(as: 'kategori', except: 'all')]
    public string $category = 'all';

    public int $page = 1;



    #[Url(as: 'sort', except: 'popular')]
    public string $sort = 'popular';

    #[Url(as: 'min', except: null)]
    public ?int $minPrice = null;

    #[Url(as: 'max', except: null)]
    public ?int $maxPrice = null;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Session]
    public string $viewMode = 'grid';

    public function mount(): void
    {
    }

    #[On('update-filters')]
    public function updateFilters($category = 'all', $search = '', $sort = 'popular', $minPrice = null, $maxPrice = null): void
    {
        $this->category = $category ?? 'all';
        $this->search = $search ?? '';
        $this->sort = $sort ?? 'popular';
        $this->minPrice = $minPrice === '' ? null : $minPrice;
        $this->maxPrice = $maxPrice === '' ? null : $maxPrice;
        $this->page = 1;
    }

    public function loadMore(): void
    {
        $this->page++;
        if (!$this->hasMore()) {
            $this->dispatch('no-more-products');
        }
    }

    private function getBaseProductQuery()
    {
        $query = Product::query()
            ->when(
                $this->category === 'promo',
                fn($q) => $q->whereHas('variants', fn($q2) => $q2->whereNotNull('active_discount_price'))
            )
            ->when(
                $this->category !== 'all' && $this->category !== 'promo',
                fn($q) => $q->whereHas('category', fn($q2) => $q2->where('name', $this->category))
            )
            ->when(
                $this->search !== '',
                fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
            );

        if ($this->minPrice !== null || $this->maxPrice !== null) {
            $query->whereHas('variants', function ($q) {
                if ($this->minPrice !== null) $q->where('price', '>=', $this->minPrice);
                if ($this->maxPrice !== null) $q->where('price', '<=', $this->maxPrice);
            });
        }

        return $query;
    }



    #[Computed]
    public function hasMore(): bool
    {
        return $this->getBaseProductQuery()->count() > ($this->page * 10);
    }

    #[Computed]
    public function products(): array
    {
        $query = $this->getBaseProductQuery()->with(['category', 'variants', 'extras']);

        if ($this->sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($this->sort === 'lowest_price') {
            $query->withMin('variants', 'price')->orderBy('variants_min_price', 'asc');
        } elseif ($this->sort === 'highest_price') {
            $query->withMin('variants', 'price')->orderBy('variants_min_price', 'desc');
        } else {
            $query->leftJoin('categories as cat_sort', 'products.category_id', '=', 'cat_sort.id')
                ->orderBy('cat_sort.order_column', 'asc')
                ->orderByRaw('products.is_active DESC')
                ->orderBy('products.id', 'asc')
                ->select('products.*');
        }

        return $query->forPage($this->page, 10)
            ->get()
            ->map(fn(Product $p) => $p->toFrontendArray())
            ->toArray();
    }
};
