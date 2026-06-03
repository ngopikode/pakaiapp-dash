<?php

use Livewire\Component;
use App\Models\RawMaterial;
use Livewire\Attributes\Layout;

new #[Layout('layouts.app')] class extends Component {
    public $materials = [];
    public $name = '';
    public $unit = 'pcs';
    public $stock = 0;
    public $cost_per_unit = 0;
    public $min_stock_alert = 0;
    public $editingId = null;

    public function mount()
    {
        $this->loadMaterials();
    }

    public function loadMaterials()
    {
        $this->materials = RawMaterial::all();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'required|numeric|min:0',
            'min_stock_alert' => 'required|numeric|min:0',
        ]);

        if ($this->editingId) {
            $material = RawMaterial::findOrFail($this->editingId);
            $material->update([
                'name' => $this->name,
                'unit' => $this->unit,
                'stock' => $this->stock,
                'cost_per_unit' => $this->cost_per_unit,
                'min_stock_alert' => $this->min_stock_alert,
            ]);
        } else {
            RawMaterial::create([
                'name' => $this->name,
                'unit' => $this->unit,
                'stock' => $this->stock,
                'cost_per_unit' => $this->cost_per_unit,
                'min_stock_alert' => $this->min_stock_alert,
            ]);
        }

        $this->resetInput();
        $this->loadMaterials();
        session()->flash('message', 'Bahan baku berhasil disimpan.');
    }

    public function edit($id)
    {
        $material = collect($this->materials)->firstWhere('id', $id);
        $this->editingId = $material->id;
        $this->name = $material->name;
        $this->unit = $material->unit;
        $this->stock = $material->stock;
        $this->cost_per_unit = $material->cost_per_unit;
        $this->min_stock_alert = $material->min_stock_alert;
    }

    public function delete($id)
    {
        RawMaterial::destroy($id);
        $this->loadMaterials();
        session()->flash('message', 'Bahan baku dihapus.');
    }

    public function resetInput()
    {
        $this->editingId = null;
        $this->name = '';
        $this->unit = 'pcs';
        $this->stock = 0;
        $this->cost_per_unit = 0;
        $this->min_stock_alert = 0;
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
                        <input type="text" wire:model="name" class="form-control" placeholder="Cth: Kopi Biji, Gula Aren">
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
                        <input type="number" step="0.01" wire:model="cost_per_unit" class="form-control">
                        @error('cost_per_unit') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Min. Stok (Alert)</label>
                        <input type="number" step="0.01" wire:model="min_stock_alert" class="form-control">
                        @error('min_stock_alert') <span class="text-danger small">{{ $message }}</span> @enderror
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
                        @forelse($materials as $item)
                        <tr>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td>
                                @if($item->stock <= $item->min_stock_alert)
                                    <span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill"></i> {{ $item->stock }}</span>
                                @else
                                    <span class="text-success">{{ $item->stock }}</span>
                                @endif
                            </td>
                            <td>{{ $item->unit }}</td>
                            <td>Rp {{ number_format($item->cost_per_unit, 0, ',', '.') }}</td>
                            <td>{{ $item->min_stock_alert }}</td>
                            <td>
                                <button wire:click="edit({{ $item->id }})" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></button>
                                <button wire:click="delete({{ $item->id }})" onclick="confirm('Yakin ingin menghapus?') || event.stopImmediatePropagation()" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
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
        </div>
    </div>
</div>
