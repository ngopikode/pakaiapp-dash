<?php

use App\Tenant\Services\OpenAiMenuService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Lazy;
use Livewire\Component;

new #[Lazy]
class extends Component
{
    public array $stats = [];

    public Collection $topProducts;

    public Collection $slowMovingProducts;

    public string $insightText = '';

    protected ?OpenAiMenuService $aiMenuService = null;

    protected function openAiMenuService(): OpenAiMenuService
    {
        return $this->aiMenuService ??= app(OpenAiMenuService::class);
    }

    public function mount(array $stats, Collection $topProducts, Collection $slowMovingProducts): void
    {
        $this->stats = $stats;
        $this->topProducts = $topProducts;
        $this->slowMovingProducts = $slowMovingProducts;

        $this->loadInsight();
    }

    public function loadInsight(): void
    {
        $dashboardData = [
            'stats' => $this->stats,
            'top_products' => $this->topProducts,
            'slow_moving_products' => $this->slowMovingProducts,
        ];

        // Cache insight per pengguna (tenant) selama 3 jam (180 menit)
        $cacheKey = 'ai_insight_tenant_' . auth()->id() . '_' . date('Y-m-d_H');

        $this->insightText = Cache::remember($cacheKey, 180 * 60, function () use ($dashboardData) {
            return $this->openAiMenuService()->generateDashboardInsight($dashboardData);
        });
    }

    public function regenerate(): void
    {
        $cacheKey = 'ai_insight_tenant_' . auth()->id() . '_' . date('Y-m-d_H');
        Cache::forget($cacheKey);
        $this->loadInsight();
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="card dash-card bg-body border w-100 p-4 mb-4 text-center" style="border-color: var(--bs-border-color-translucent) !important; min-height: 180px;">
            <div class="d-flex flex-column align-items-center justify-content-center py-3 gap-3 h-100">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem; animation-duration: 0.8s;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-secondary small fw-bold animate-pulse" style="letter-spacing: 1px;">✨ AI sedang menganalisis performa toko Anda...</div>
            </div>
        </div>
        HTML;
    }
};
