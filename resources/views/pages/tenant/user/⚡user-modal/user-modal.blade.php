<div>
    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 1.25rem;">

                <div class="modal-header border-bottom pb-3 pt-4 px-4"
                     style="border-top-left-radius: 1.25rem; border-top-right-radius: 1.25rem;">
                    <h5 class="modal-title fw-bold text-dark mb-0">
                        <i class="bi bi-person-badge me-2"></i> {{ $isEditing ? 'Edit User' : 'Tambah User Baru' }}
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body px-4 pt-4 pb-4"
                     style="border-bottom-left-radius: 1.25rem; border-bottom-right-radius: 1.25rem;">
                    <form wire:submit.prevent="save">

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   wire:model="name" placeholder="Contoh: Budi Santoso" style="border-radius: 0.75rem;">
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Alamat Email / Username</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   wire:model="email" placeholder="budi@toko.com" style="border-radius: 0.75rem;">
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Password Login</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   wire:model="password"
                                   placeholder="{{ $isEditing ? 'Kosongkan jika tidak ingin ganti' : 'Minimal 6 karakter' }}"
                                   style="border-radius: 0.75rem;">
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold">Hak Akses (Role)</label>
                            <select class="form-select @error('role') is-invalid @enderror" wire:model="role"
                                    style="border-radius: 0.75rem;"
                                    {{ ($isEditing && $role === 'manager') ? 'disabled' : '' }}>
                                <option value="cashier">Kasir (Transaksi & Kasir)</option>
                                <option value="kitchen">Dapur (Hanya Layar KDS)</option>
                                @if($isEditing && $role === 'manager')
                                    <option value="manager">Manager (Akses Penuh)</option>
                                @endif
                            </select>
                            @error('role') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary fw-bold"
                                    style="border-radius: 0.75rem;" data-bs-dismiss="modal">Batal
                            </button>
                            <button type="submit" class="btn btn-dark fw-bold shadow-sm d-flex align-items-center gap-2"
                                    style="border-radius: 0.75rem;" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="save"><i class="bi bi-save"></i> Simpan</span>
                                <span wire:loading wire:target="save"><small
                                        class="spinner-border spinner-border-sm"></small> Menyimpan...</span>
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
    $wire.on('show-user-modal', () => {
        const userModalElement = document.getElementById('userModal');
        const userModal = bootstrap.Modal.getOrCreateInstance(userModalElement);
        userModal.show();
    });

    $wire.on('hide-user-modal', () => {
        const userModalElement = document.getElementById('userModal');
        const userModal = bootstrap.Modal.getOrCreateInstance(userModalElement);
        userModal.hide();
    });
</script>
@endscript
