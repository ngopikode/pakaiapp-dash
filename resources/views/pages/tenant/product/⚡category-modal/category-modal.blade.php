<div>
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true" wire:ignore.self
         x-data="{ modal: null }"
         x-init="
            modal = new bootstrap.Modal($el);
            $wire.on('show-category-modal', () => modal.show());
            $wire.on('hide-category-modal', () => modal.hide());
         ">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">

                <div class="modal-header border-0 pt-4 px-4 pb-0">
                    <h5 class="modal-title fw-bold font-serif text-dark">
                        <i class="bi bi-folder2-open me-2 text-brand"></i>
                        {{ $isEditing ? 'Edit Kategori' : 'Kategori Baru' }}
                    </h5>
                    <button type="button" class="btn-close shadow-none rounded-circle p-2" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-3 pb-4">
                    <form wire:submit.prevent="save">

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Nama Kategori</label>
                            <input type="text"
                                   class="form-control rounded-pill px-3 py-2 {{ $errors->has('name') ? 'is-invalid border-danger' : '' }}"
                                   wire:model="name"
                                   placeholder="Contoh: Atasan, Kopi, dsb.">
                            @error('name') <span class="invalid-feedback ps-3">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4" x-data="{ type: @entangle('type') }">
                            <label class="form-label small text-muted fw-bold">Tipe Kategori (Menentukan Form
                                Input)</label>

                            <div class="d-flex gap-3">
                                <label
                                    @click="type = 'retail'"
                                    :class="type === 'retail' ? 'border-brand bg-brand-light' : 'bg-light'"
                                    class="flex-grow-1 border rounded-4 p-3 cursor-pointer position-relative text-center">

                                    <input type="radio" value="retail" x-model="type"
                                           class="position-absolute opacity-0">

                                    <i class="bi bi-bag fs-4 d-block mb-1"
                                       :class="type === 'retail' ? 'text-brand' : 'text-muted'"></i>

                                    <small class="small fw-bold"
                                           :class="type === 'retail' ? 'text-brand' : 'text-muted'">
                                        Retail (Baju, Barang)
                                    </small>
                                </label>

                                <label
                                    @click="type = 'fnb'"
                                    :class="type === 'fnb' ? 'border-brand bg-brand-light' : 'bg-light'"
                                    class="flex-grow-1 border rounded-4 p-3 cursor-pointer position-relative text-center">

                                    <input type="radio" value="fnb" x-model="type" class="position-absolute opacity-0">

                                    <i class="bi bi-cup-hot fs-4 d-block mb-1"
                                       :class="type === 'fnb' ? 'text-brand' : 'text-muted'"></i>

                                    <small class="small fw-bold"
                                           :class="type === 'fnb' ? 'text-brand' : 'text-muted'">
                                        F&B (Makanan/Minuman)
                                    </small>
                                </label>
                            </div>

                            @error('type')
                            <span class="text-danger small ps-3 mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold border"
                                    data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit"
                                    class="btn btn-brand rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center gap-2"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save"><i
                                        class="bi bi-cloud-check"></i> Simpan</span>
                                <span wire:loading wire:target="save">
                                    <small class="spinner-border spinner-border-sm" aria-hidden="true"></small> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
