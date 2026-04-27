<?php

namespace App\Livewire\Tenant\Product;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public array $categories = [];
    public string $selectedCategoryType = 'retail';

    // Data Umum Produk
    public $name = '';
    public $category_id = '';
    public $description = '';
    public $image;
    public $tax_included = false;
    public $is_active = true;

    // Data Base (Jika Varian OFF)
    public $has_variants = false;
    public $base_cost = 0;
    public $base_price = 0;
    public $base_stock = 0;
    public $base_min_stock = 0;

    // Array Varian & Ekstra
    public $variants = [];
    public $extras = [];

    public function mount()
    {
        // Asumsi kamu punya scope atau relasi berdasarkan tenant/restaurant
        // Sesuaikan query ini dengan logic multitenant kamu (misal nambah where('restaurant_id', ...))
        $this->categories = Category::select('id', 'name', 'type')->orderBy('name')->get()->toArray();

        // Inisialisasi baris kosong
        $this->addVariant();
        $this->addExtra();
    }

    // Deteksi tipe kategori (F&B / Retail)
    public function updatedCategoryId($value)
    {
        $cat = collect($this->categories)->firstWhere('id', $value);
        $this->selectedCategoryType = $cat['type'] ?? 'retail';
    }

    // --- MANAJEMEN VARIAN ---
    public function addVariant()
    {
        $this->variants[] = ['name' => '', 'cost' => '', 'price' => '', 'stock' => '', 'min_stock' => ''];
    }

    public function removeVariant($index)
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    // --- MANAJEMEN EKSTRA (Khusus F&B) ---
    public function addExtra()
    {
        $this->extras[] = ['name' => '', 'cost' => '', 'price' => ''];
    }

    public function removeExtra($index)
    {
        unset($this->extras[$index]);
        $this->extras = array_values($this->extras);
    }

    // --- PROSES SIMPAN ---
    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'Nama menu wajib diisi',
            'category_id.required' => 'Pilih kategori terlebih dahulu',
        ]);

        DB::beginTransaction();
        try {
            $imagePath = $this->image ? $this->image->store('products', 'public') : null;

            // 1. Simpan Produk Induk
            $product = Product::create([
                'category_id' => $this->category_id,
                'name' => $this->name,
                'description' => $this->description,
                'image' => $imagePath,
                'tax_included' => $this->tax_included,
                'has_variants' => $this->has_variants,
                'is_active' => $this->is_active,
                // Data base dikosongkan jika ada varian
                'base_cost' => !$this->has_variants ? ($this->base_cost ?: 0) : 0,
                'base_price' => !$this->has_variants ? ($this->base_price ?: 0) : 0,
                'stock' => !$this->has_variants ? ($this->base_stock ?: 0) : 0,
                'min_stock' => !$this->has_variants ? ($this->base_min_stock ?: 0) : 0,
            ]);

            // 2. Simpan Varian
            if ($this->has_variants) {
                foreach ($this->variants as $variant) {
                    if (!empty($variant['name'])) {
                        $product->variants()->create([
                            'name' => $variant['name'],
                            'cost' => $variant['cost'] ?: 0,
                            'price' => $variant['price'] ?: 0,
                            'stock' => $variant['stock'] ?: 0,
                            'min_stock' => $variant['min_stock'] ?: 0,
                        ]);
                    }
                }
            } else {
                // Logika Varian Default (Hidden Variant)
                $product->variants()->create([
                    'name' => 'Default',
                    'cost' => $this->base_cost ?: 0,
                    'price' => $this->base_price ?: 0,
                    'stock' => $this->base_stock ?: 0,
                    'min_stock' => $this->base_min_stock ?: 0,
                ]);
            }

            // 3. Simpan Ekstra / Add-ons (Jika Kategori F&B)
            if ($this->selectedCategoryType === 'fnb') {
                foreach ($this->extras as $extra) {
                    if (!empty($extra['name'])) {
                        $product->extras()->create([
                            'name' => $extra['name'],
                            'cost' => $extra['cost'] ?: 0,
                            'price' => $extra['price'] ?: 0,
                            'is_active' => true,
                        ]);
                    }
                }
            }

            DB::commit();

            // Flash session untuk notifikasi di halaman selanjutnya
            session()->flash('success', 'Produk ' . $this->name . ' berhasil ditambahkan! ☕');

            // Redirect ke halaman list menu (Asumsi nama route-mu menu.index)
            $this->redirectRoute('product', navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
};
