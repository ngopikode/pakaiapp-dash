<?php

use App\Models\TenantUser;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'cashier';

    public bool $isEditing = false;

    #[On('openModal')]
    public function openModal($userId = null): void
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'password', 'role']);

        if ($userId) {
            $this->isEditing = true;
            $this->userId = $userId;
            $user = TenantUser::find($userId);
            if ($user) {
                $this->name = $user->name;
                $this->email = $user->email;
                $this->role = $user->role ?? 'cashier';
            }
        } else {
            $this->isEditing = false;
            $this->userId = null;
        }

        $this->dispatch('show-user-modal');
        $this->dispatch('show-bootstrap-modal');
    }

    public function save(): void
    {
        // Validasi Dinamis (Password wajib saat Create, opsional saat Edit)
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->userId,
        ];

        if ($this->isEditing && $this->role === 'manager') {
            $rules['role'] = 'required|in:manager';
        } else {
            $rules['role'] = 'required|in:cashier';
            $this->role = 'cashier'; // Force role to cashier for new creations or if trying to escalate
        }
        
        if (!$this->isEditing || !empty($this->password)) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];

        // Jika password diisi, enkripsi dan simpan
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        TenantUser::updateOrCreate(['id' => $this->userId], $data);

        $this->dispatch('user-saved');
        $this->dispatch('hide-user-modal');
        $this->dispatch('notify', message: $this->isEditing ? 'Data User diupdate!' : 'User baru ditambahkan!');
    }
};
