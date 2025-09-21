<?php

use Livewire\Volt\Component;

new class extends Component {
    public string $selectedTab = 'Login';
    public bool $showModal = false;
}; ?>

<div>



    <x-modal wire:model="showModal" title="Hey" class="backdrop-blur">
        Press `ESC`, click outside or click `CANCEL` to close.

        <x-tabs wire:model="selectedTab">
            <x-tab name="login-tab" label="Login" icon="o-users">
                <div>Login</div>
            </x-tab>
            <x-tab name="register-tab" label="Register" icon="o-sparkles">
                <div>Register</div>
            </x-tab>
        </x-tabs>

        <x-button label="Change to Musics" @click="$wire.selectedTab = 'login-tab'" />

        <x-slot:actions>
            <x-button label="Cancel" @click="$wire.showModal = false" />
        </x-slot:actions>
    </x-modal>

    <x-button label="Open" @click="$wire.showModal = true" />
</div>
