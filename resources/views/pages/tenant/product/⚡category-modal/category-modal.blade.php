<div>
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">

                <div class="modal-header border-bottom pb-3 pt-4 px-4 bg-body-tertiary"
                     style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h5 class="modal-title fw-bold font-serif text-primary mb-0">
                        <i class="bi bi-folder2-open me-2"></i>
                        {{ $isEditing ? 'Edit Kategori' : 'Kategori Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-4 pb-4 bg-body"
                     style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                    <form wire:submit.prevent="save">

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold">Nama Kategori</label>
                            <input type="text"
                                   class="form-control bg-body-tertiary {{ $errors->has('name') ? 'is-invalid border-danger' : '' }}"
                                   style="border-radius: 0.75rem;"
                                   wire:model="name"
                                   placeholder="Contoh: Atasan, Kopi, dsb.">
                            @error('name') <span class="invalid-feedback ps-2">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4" x-data="{ type: @entangle('type') }">
                            <label class="form-label small text-muted fw-bold">Tipe Kategori</label>

                            <div class="d-flex gap-3">
                                <label @click="type = 'retail'"
                                       :class="type === 'retail' ? 'border-primary bg-primary bg-opacity-10' : 'bg-body-secondary border-transparent'"
                                       class="flex-grow-1 border p-3 cursor-pointer position-relative text-center transition-all"
                                       style="border-radius: 1rem;">
                                    <input type="radio" value="retail" x-model="type"
                                           class="position-absolute opacity-0">
                                    <i class="bi bi-bag fs-4 d-block mb-1"
                                       :class="type === 'retail' ? 'text-primary' : 'text-muted'"></i>
                                    <span class="small fw-bold"
                                          :class="type === 'retail' ? 'text-primary' : 'text-muted'">Retail</span>
                                </label>

                                <label @click="type = 'fnb'"
                                       :class="type === 'fnb' ? 'border-primary bg-primary bg-opacity-10' : 'bg-body-secondary border-transparent'"
                                       class="flex-grow-1 border p-3 cursor-pointer position-relative text-center transition-all"
                                       style="border-radius: 1rem;">
                                    <input type="radio" value="fnb" x-model="type" class="position-absolute opacity-0">
                                    <i class="bi bi-cup-hot fs-4 d-block mb-1"
                                       :class="type === 'fnb' ? 'text-primary' : 'text-muted'"></i>
                                    <span class="small fw-bold" :class="type === 'fnb' ? 'text-primary' : 'text-muted'">F&B</span>
                                </label>
                            </div>
                            @error('type') <span
                                class="text-danger small ps-2 mt-1 d-block">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-4 border-top">
                            <button type="button" class="btn btn-outline-secondary fw-bold"
                                    style="border-radius: 0.75rem;" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit"
                                    class="btn btn-primary fw-bold shadow-sm d-flex align-items-center gap-2"
                                    style="border-radius: 0.75rem;" wire:loading.attr="disabled">
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

@script
<script>
    $wire.on('show-category-modal', () => {
        const modalElement = document.getElementById('categoryModal');
        const categoryModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        categoryModal.show();
        window.hideLoader();
    });

    $wire.on('hide-category-modal', () => {
        const modalElement = document.getElementById('categoryModal');
        const categoryModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        categoryModal.hide();
    });
</script>
@endscript
