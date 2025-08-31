<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

new class extends Component {
    #[Validate('required|email')]
    public $email = '';

    #[Validate('required')]
    public $password = '';

    public function login()
    {
        $this->validate();

        if (
            !Auth::attempt(
                [
                    'email' => $this->email,
                    'password' => $this->password,
                ],
                true,
            )
        ) {
            // "true" = remember me
            throw ValidationException::withMessages([
                'email' => 'The provided credentials are incorrect.',
                'password' => 'The provided credentials are incorrect.',
            ]);
        }

        // ✅ Redirect after successful login
        return redirect()->intended('/dashboard');
    }
}; ?>

<div>
    <x-form class="w-[50%]" wire:submit="login">
        <x-input label="Email" wire:model="email" />
        <x-input label="Password" wire:model="password" type="password" />

        <x-slot:actions>
            <x-button label="Don't have an account? Sign Up" no-wire-navigate link="/register" />
            <x-button label="Login" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>
