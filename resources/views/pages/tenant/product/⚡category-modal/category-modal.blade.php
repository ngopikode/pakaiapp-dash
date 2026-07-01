<div>
    <div class="modal fade modal-bottom-mobile" id="categoryModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg" style="border-radius: 1.5rem;">

                {{-- Header --}}
                <div class="modal-header border-bottom px-4 py-3" style="border-radius: 1.5rem 1.5rem 0 0;">
                    <h5 class="modal-title fw-bold text-dark mb-0 d-flex align-items-center">
                        <i class="bi bi-folder2-open text-warning me-2 fs-4"></i>
                        {{ $isEditing ? 'Edit Kategori' : 'Kategori Baru' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body Form --}}
                <form wire:submit.prevent="save">
                    <div class="modal-body p-4">

                        <!-- Input Nama pakai Floating Label ala SaaS -->
                        <div class="mb-4">
                            <div class="form-floating">
                                <input type="text"
                                       class="form-control fw-bold border-0 shadow-sm @error('name') is-invalid @enderror"
                                       id="categoryName"
                                       wire:model="name"
                                       placeholder="Nama Kategori"
                                       style="border-radius: 1rem;">
                                <label for="categoryName" class="text-muted fw-medium">Nama Kategori</label>
                            </div>
                            @error('name')
                            <span class="text-danger small fw-bold mt-1 d-block ps-2">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- Footer Actions --}}
                    <div class="modal-footer border-top p-3" style="border-radius: 0 0 1.5rem 1.5rem;">
                        <div class="d-flex w-100 gap-2">
                            <button type="button"
                                    class="btn btn-white border fw-bold flex-shrink-0 rounded-pill shadow-sm px-4"
                                    data-bs-dismiss="modal">
                                Batal
                            </button>
                            <button type="submit"
                                    class="btn btn-primary fw-bold flex-grow-1 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2"
                                    wire:loading.attr="disabled"
                                    style="background: #F97316; border: none;">
                                <span wire:loading.remove wire:target="save">
                                    <i class="bi bi-check2-circle"></i> Simpan
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Menyimpan...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

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

        // Optional: Call hideLoader if it exists
        if (typeof window.hideLoader === 'function') window.hideLoader();
    });

    $wire.on('hide-category-modal', () => {
        const modalElement = document.getElementById('categoryModal');
        const categoryModal = bootstrap.Modal.getOrCreateInstance(modalElement);
        categoryModal.hide();
    });
</script>
@endscript
