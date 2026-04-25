<?php

use App\Models\StoreSetting;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::guest', ['title' => 'Posts Dashboard'])]
class extends Component {
    public array $form = [
        'email' => '',
        'password' => '',
        'remember' => false,
    ];

    public function login(): void
    {
        $this->validate([
            'form.email' => ['required', 'string', 'email'],
            'form.password' => ['required', 'string'],
        ]);

        $credentials = [
            'email' => $this->form['email'],
            'password' => $this->form['password'],
        ];

        if (Auth::attempt($credentials, $this->form['remember'])) {
            session()->regenerate();

            $this->redirect('/dashboard');
        }

        $this->addError('form.email', 'Email atau password yang dimasukkan salah.');
    }

    #[Computed]
    public function settings(): ?StoreSetting
    {
        return StoreSetting::first();
    }
};
