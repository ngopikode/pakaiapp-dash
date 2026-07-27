<?php

use App\Livewire\Forms\ProfileForm;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Profil')]
class extends Component
{
    public ProfileForm $form;

    public bool $showSuccessMessage = false;

    public function mount(): void
    {
        $this->form->setUser(Auth::user());
    }

    public function updateProfileInformation(): void
    {
        $this->form->updateProfile();
        $this->showSuccessMessage = true;
    }

    public function updatePassword(): void
    {
        $this->form->updatePassword();
        $this->showSuccessMessage = true;

        // Kosongkan input password setelah berhasil diubah
        $this->form->current_password = '';
        $this->form->password = '';
        $this->form->password_confirmation = '';
    }
};
