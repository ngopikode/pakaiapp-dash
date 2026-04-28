<?php

use App\Models\TenantUser;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public int $perPage = 10;

    #[On('user-saved')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->perPage = 10;
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    public function deleteUser($id): void
    {
        $user = User::find($id);

        // Mencegah user menghapus dirinya sendiri (asumsi auth login)
        if (auth()->id() === $user->id) {
            $this->dispatch('notify', message: 'Anda tidak bisa menghapus akun Anda sendiri!', type: 'error');
            return;
        }

        if ($user) {
            $user->delete();
            $this->dispatch('notify', message: 'User berhasil dihapus!');
        }
    }

    public function with(): array
    {
        $users = TenantUser::query()
            ->when($this->search)->where(
                fn($query) => $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        return [
            'users' => $users,
            'totalUsers' => User::count(),
            'managerCount' => User::where('role', 'manager')->count(),
            'cashierCount' => User::where('role', 'cashier')->count(),
        ];
    }

};
