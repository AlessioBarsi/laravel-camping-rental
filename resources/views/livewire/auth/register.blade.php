<?php

use Livewire\Volt\Component;
use App\Models\User;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;
use Illuminate\Support\Facades\Hash;

new class extends Component {
    use Toast;

    #[Validate('required|min:6|max:20')]
    public $name = '';

    #[Validate('required|min:6|max:20|email|unique:users')]
    public $email = '';

    #[Validate('required|min:9|max:50|same:confirmpassword')]
    public $password = '';

    #[Validate('required|min:9|max:50|same:password')]
    public $confirmpassword = '';

    public function register()
    {
        $this->validate();

        try {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),

            ]);
            $this->success('User account created');
            sleep(0.5);
            
        } catch (\Throwable $th) {
            $this->error("Something went wrong: ".$th->getMessage());
        }
  
    }
}; ?>

<div>
    <x-form class="w-[50%]" wire:submit="register">
        <x-input label="Username" wire:model="name" />
        <x-input label="Email" wire:model="email" type="email" />
        <x-input label="Password" wire:model="password" type="password" />
        <x-input label="Confirm Password" wire:model="confirmpassword" type="password" />

        <x-slot:actions>
            <x-button label="Already have an account? Login" no-wire-navigate link="/login"/>
            <x-button label="Sign Up" class="btn-primary" type="submit" spinner="save" />
        </x-slot:actions>
    </x-form>
</div>
