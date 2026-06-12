<?php

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\AiPricingRule;
use Livewire\Component;

new class extends Component
{
    // Mode Auto-Pilot Global Toggle
    public $isEngineActive = false;

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

    #[\Livewire\Attributes\Computed]
    public function products()
    {
        return Product::where('is_active', true)
            ->with(['variants' => function($query) {
                $query->where('stock', '>', 0);
            }])->get();
    }

    #[\Livewire\Attributes\Computed]
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

    public function generateAiSuggestion(\App\Services\OpenAiMenuService $aiService)
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
?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3 class="mb-0">
                <i class="bi bi-robot text-primary me-2"></i> AI Menu Engine Manager
            </h3>
            <div class="form-check form-switch fs-4">
                <input class="form-check-input" type="checkbox" role="switch" id="engineToggle" wire:model.live="isEngineActive">
                <label class="form-check-label fs-6 mt-1 ms-2" for="engineToggle">
                    {{ $isEngineActive ? 'Auto-Pilot Aktif' : 'Auto-Pilot Mati' }}
                </label>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Buat Pricing Rule Baru</h5>
                </div>
                <div class="card-body">
                    <!-- AI Generator Block -->
                    <div class="card shadow-none border border-primary mb-4" style="background-color: #f8fbff;">
                        <div class="card-body p-3">
                            <h6 class="text-primary fw-bold mb-2"><i class="bi bi-stars"></i> Asisten AI Promosi</h6>
                            <p class="small text-muted mb-2">Ketik tujuan bisnismu hari ini, dan AI akan otomatis mengisi form di bawah dengan strategi yang paling cocok.</p>
                            <div class="input-group">
                                <input type="text" class="form-control border-primary" wire:model="aiPrompt" placeholder="Misal: Bikin promo gila-gilaan buat ngabisin stok dimsum malam ini...">
                                <button class="btn btn-primary px-4" type="button" wire:click="generateAiSuggestion" wire:loading.attr="disabled" wire:target="generateAiSuggestion">
                                    <span wire:loading.remove wire:target="generateAiSuggestion">Tanya AI ✨</span>
                                    <span wire:loading wire:target="generateAiSuggestion"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span></span>
                                </button>
                            </div>
                            @error('aiPrompt') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @if (session()->has('ai_success'))
                                <div class="alert alert-success mt-2 mb-0 py-2 small"><i class="bi bi-check-circle-fill me-1"></i> {{ session('ai_success') }}</div>
                            @endif
                            @if (session()->has('ai_error'))
                                <div class="alert alert-danger mt-2 mb-0 py-2 small"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('ai_error') }}</div>
                            @endif
                        </div>
                    </div>

                    <form wire:submit="saveRules">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Nama Rule</label>
                                <input type="text" class="form-control" wire:model="ruleName" placeholder="Contoh: Happy Hour Anti Sepi">
                                @error('ruleName') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tipe Diskon</label>
                                <select class="form-select" wire:model="ruleType">
                                    <option value="percentage">Persentase (%)</option>
                                    <option value="fixed_cut">Potongan Harga (Rp)</option>
                                </select>
                                @error('ruleType') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nilai Diskon</label>
                                <input type="number" class="form-control" wire:model="discountValue" placeholder="Contoh: 20">
                                @error('discountValue') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" class="form-control" wire:model="startTime">
                                @error('startTime') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jam Berakhir</label>
                                <input type="time" class="form-control" wire:model="endTime">
                                @error('endTime') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Hari Aktif</label>
                            @php
                                $days = ['Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu', 'Sun' => 'Minggu'];
                            @endphp
                            <div class="d-flex flex-wrap gap-3">
                                @foreach($days as $val => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $val }}" id="day_{{ $val }}" wire:model="activeDays">
                                        <label class="form-check-label" for="day_{{ $val }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('activeDays') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Target Produk & Varian</label>
                            <div class="card bg-light">
                                <div class="card-body" style="max-height: 250px; overflow-y: auto;">
                                    @foreach($this->products as $product)
                                        @if($product->variants->count() > 0)
                                            <div class="mb-3">
                                                <strong>{{ $product->name }}</strong>
                                                <div class="ms-3 mt-2">
                                                    @foreach($product->variants as $variant)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="{{ $variant->id }}" id="var_{{ $variant->id }}" wire:model="selectedVariants">
                                                            <label class="form-check-label" for="var_{{ $variant->id }}">
                                                                {{ $variant->name }} (Rp {{ number_format($variant->price, 0, ',', '.') }})
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @error('selectedVariants') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-outline-secondary" wire:click="$refresh">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <span wire:loading.remove wire:target="saveRules">Simpan Aturan AI</span>
                                <span wire:loading wire:target="saveRules">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-magic me-2"></i> Preset Cerdas AI</h5>
                    <p class="card-text small mb-3">Gunakan template strategi harga yang terbukti meningkatkan penjualan di jam-jam tertentu.</p>
                    <button class="btn btn-light w-100 mb-2 text-start" wire:click="loadPresetConfig('happy_hour')">
                        <strong>🍻 Happy Hour Anti Sepi</strong>
                        <div class="small text-muted mt-1">Diskon 20% jam 14:00 - 16:00 (Senin-Jumat)</div>
                    </button>
                    <button class="btn btn-outline-light w-100 text-start" disabled>
                        <strong>🌙 Flash Sale Midnight</strong>
                        <div class="small mt-1">Potongan 10rb jam 21:00 - 23:59 (Weekend) - <em class="text-warning">Coming Soon</em></div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Aturan Tersimpan -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Daftar Aturan AI yang Tersimpan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nama Rule</th>
                                    <th>Diskon</th>
                                    <th>Jadwal Aktif</th>
                                    <th>Target Menu</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->savedRules as $savedRule)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $savedRule->rule_name }}</td>
                                        <td>
                                            @if($savedRule->rule_type === 'percentage')
                                                <span class="badge bg-success">{{ $savedRule->productVariants->first()?->pivot->discount_value ?? 0 }}%</span>
                                            @else
                                                <span class="badge bg-info">Rp {{ number_format($savedRule->productVariants->first()?->pivot->discount_value ?? 0, 0, ',', '.') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small fw-bold">
                                                <i class="bi bi-clock me-1 text-muted"></i> {{ substr($savedRule->start_time, 0, 5) }} - {{ substr($savedRule->end_time, 0, 5) }}
                                            </div>
                                            <div class="small text-muted mt-1" style="font-size: 0.75rem;">
                                                {{ is_array($savedRule->active_days) ? implode(', ', $savedRule->active_days) : $savedRule->active_days }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary rounded-pill">{{ $savedRule->productVariants->count() }} Varian</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-sm btn-outline-danger" wire:click="deleteRule({{ $savedRule->id }})" wire:confirm="Yakin ingin menghapus aturan promo ini?">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-2"></i> 
                                            Belum ada aturan diskon AI yang aktif.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>