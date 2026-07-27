<?php

use App\Tenant\Models\Ai\AiPricingRule;
use App\Tenant\Models\Core\Product;
use App\Tenant\Services\OpenAiMenuService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('AI - Hidup Jok**')]
class extends Component
{
    // Mode Auto-Pilot Global Toggle
    public bool $isEngineActive = false;

    // Form inputs
    public $aiPrompt = '';

    public $ruleName = '';

    public $ruleType = 'percentage';

    public $discountValue = '';

    public $startTime = '';

    public $endTime = '';

    public $activeDays = [];

    public $selectedVariants = [];

    protected function rules()
    {
        return [
            'ruleName' => 'required|string|max:255',
            'ruleType' => 'required|in:percentage,fixed_cut',
            'discountValue' => 'required|numeric|min:1',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'activeDays' => 'required|array|min:1',
            'selectedVariants' => 'required|array|min:1',
        ];
    }

    protected function messages()
    {
        return [
            'ruleName.required' => 'Nama aturan wajib diisi.',
            'ruleType.required' => 'Tipe diskon wajib dipilih.',
            'discountValue.required' => 'Nilai diskon wajib diisi.',
            'discountValue.numeric' => 'Nilai diskon harus berupa angka.',
            'discountValue.min' => 'Nilai diskon minimal 1.',
            'startTime.required' => 'Jam mulai wajib diisi.',
            'endTime.required' => 'Jam berakhir wajib diisi.',
            'endTime.after' => 'Jam berakhir harus lebih dari jam mulai.',
            'activeDays.required' => 'Pilih minimal satu hari aktif.',
            'selectedVariants.required' => 'Pilih minimal satu produk atau varian target.',
        ];
    }

    #[Computed]
    public function products()
    {
        return Product::where('is_active', true)
            ->with(['variants' => function ($query) {
                $query->where('stock', '>', 0);
            }])->get();
    }

    #[Computed]
    public function savedRules()
    {
        return AiPricingRule::with('productVariants')->orderBy('created_at', 'desc')->get();
    }

    public function deleteRule($id)
    {
        $rule = AiPricingRule::findOrFail($id);
        $rule->productVariants()->detach();
        $rule->delete();
        session()->flash('success', 'Aturan berhasil dihapus!');
    }

    public function mount()
    {
        // Load global setting if any (placeholder, for now just false)
        $this->isEngineActive = false;
    }

    public function loadPresetConfig($preset)
    {
        if ($preset === 'happy_hour') {
            $this->ruleName = 'Happy Hour Anti Sepi';
            $this->ruleType = 'percentage';
            $this->discountValue = 20; // 20% discount
            $this->startTime = '14:00';
            $this->endTime = '16:00';
            $this->activeDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        }
    }

    public function generateAiSuggestion(OpenAiMenuService $aiService)
    {
        $this->validate(['aiPrompt' => 'required|string|min:3']);

        $menuData = $this->products->map(function ($product) {
            return [
                'name' => $product->name,
                'variants' => $product->variants->map(function ($variant) {
                    return [
                        'id' => $variant->id,
                        'name' => $variant->name,
                        'price' => $variant->price,
                    ];
                })->toArray(),
            ];
        })->toArray();

        $suggestion = $aiService->generateMerchantStrategy($this->aiPrompt, $menuData);

        if (!empty($suggestion)) {
            $this->ruleName = $suggestion['ruleName'] ?? $this->ruleName;
            $this->ruleType = $suggestion['ruleType'] ?? $this->ruleType;
            $this->discountValue = $suggestion['discountValue'] ?? $this->discountValue;
            $this->startTime = $suggestion['startTime'] ?? $this->startTime;
            $this->endTime = $suggestion['endTime'] ?? $this->endTime;
            $this->activeDays = $suggestion['activeDays'] ?? $this->activeDays;
            $this->selectedVariants = $suggestion['suggestedVariantIds'] ?? [];

            session()->flash('ai_success', 'AI berhasil membuatkan strategi untuk Anda! Silakan review sebelum menyimpan.');
        } else {
            session()->flash('ai_error', 'AI gagal memberikan saran. Silakan coba lagi.');
        }
    }

    public function saveRules()
    {
        $this->validate();

        $rule = AiPricingRule::create([
            'rule_name' => $this->ruleName,
            'rule_type' => $this->ruleType,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'active_days' => $this->activeDays,
            'is_active' => true,
        ]);

        // Attach variants with the discount value
        $syncData = [];
        foreach ($this->selectedVariants as $variantId) {
            $syncData[$variantId] = ['discount_value' => $this->discountValue];
        }
        $rule->productVariants()->attach($syncData);

        session()->flash('success', 'AI Pricing Rule berhasil disimpan!');

        $this->reset(['ruleName', 'ruleType', 'discountValue', 'startTime', 'endTime', 'activeDays', 'selectedVariants']);
    }
};
