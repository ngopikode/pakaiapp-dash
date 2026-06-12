<?php

use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\RawMaterial;

new #[Title("Resep")]
class extends Component {
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:50')]
    public string $unit = 'pcs';

    #[Validate('required|numeric|min:0')]
    public float $stock = 0;

    #[Validate('required|numeric|min:0')]
    public float $costPerUnit = 0;

    #[Validate('required|numeric|min:0')]
    public float $minStockAlert = 0;

    public ?int $editingId = null;

    public int $perPage = 10;

    #[Computed]
    public function materials(): RawMaterial|Collection
    {
        return RawMaterial::latest()->take($this->perPage)->get();
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function save(): void
    {
        $validated = $this->validate();

        RawMaterial::updateOrCreate(
            ['id' => $this->editingId],
            $validated
        );

        $this->resetInput();
        session()->flash('message', 'Bahan baku berhasil disimpan.');
    }

    public function edit(RawMaterial $material): void
    {
        $this->editingId = $material->id;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->stock = (float)$material->stock;
        $this->costPerUnit = (float)$material->costPerUnit;
        $this->minStockAlert = (float)$material->minStockAlert;
    }

    public function delete(RawMaterial $material): void
    {
        $material->delete();
        session()->flash('message', 'Bahan baku dihapus.');
    }

    public function resetInput(): void
    {
        $this->reset(['name', 'stock', 'costPerUnit', 'minStockAlert', 'editingId']);
        $this->unit = 'pcs';
    }
}; ?>

<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Manajemen Bahan Baku & Resep
        </h2>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ $editingId ? 'Edit Bahan Baku' : 'Tambah Bahan Baku' }}</h5>
            <form wire:submit="save">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Bahan</label>
                        <input type="text" wire:model="name" class="form-control"
                               placeholder="Cth: Kopi Biji, Gula Aren">
                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Satuan</label>
                        <select wire:model="unit" class="form-select">
                            <option value="pcs">Pcs</option>
                            <option value="gram">Gram</option>
                            <option value="kg">Kg</option>
                            <option value="ml">Ml</option>
                            <option value="liter">Liter</option>
                        </select>
                        @error('unit') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Stok Saat Ini</label>
                        <input type="number" step="0.01" wire:model="stock" class="form-control">
                        @error('stock') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Harga Modal (/satuan)</label>
                        <input type="number" step="0.01" wire:model="costPerUnit" class="form-control">
                        @error('costPerUnit') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Min. Stok (Alert)</label>
                        <input type="number" step="0.01" wire:model="minStockAlert" class="form-control">
                        @error('minStockAlert') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                    @if($editingId)
                        <button type="button" wire:click="resetInput" class="btn btn-light">Batal</button>
                    @endif
                </div>
                @if (session()->has('message'))
                    <div class="text-success mt-2 small">{{ session('message') }}</div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">Daftar Bahan Baku</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Nama Bahan</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Harga Modal/Satuan</th>
                        <th>Alert Stok</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($this->materials as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td>
                                @if($item->stock <= $item->minStockAlert)
                                    <span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> {{ $item->stock }}</span>
                                @else
                                    <span class="text-success">{{ $item->stock }}</span>
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>Rp {{ number_format($item->costPerUnit, 0, ',', '.') }}</td>
                            <td>{{ $item->minStockAlert }}</td>
                            <td>
                                <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-outline-secondary"><i
                                        class="bi bi-pencil"></i></button>
                                <button wire:click="delete({{ $item->id }})"
                                        wire:confirm="Yakin ingin menghapus?"
                                        class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Belum ada bahan baku.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div x-intersect="$wire.loadMore()" class="text-center mt-3">
                <div wire:loading wire:target="loadMore" class="spinner-border spinner-border-sm text-primary"
                     role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
