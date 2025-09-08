<?php

use Livewire\Volt\Component;

new class extends Component {
    public $step = 1;
}; ?>

<div>
    <div class="text-5xl font-bold my-2 w-auto text-center">Laravel Camping Rental</div>
    <div class="flex items-start space-x-5 my-3">

        <x-card class="w-[30%]" title="Rent Camping Gear Easy"
            subtitle="Affordable, high-quality equipment for your next adventure" shadow separator>
            <x-button label="Browse Stores" class="btn-primary" />
            <x-button label="Sign Up" class="mx-3 underline" />
        </x-card>

        <x-card title="How it works" class="w-[70%]" shadow separator>
            <div x-data="{ step: @entangle('step') }">
                <x-steps wire:model="step" class="border-y border-base-content/10 my-5 py-5" steps-color="step-primary">
                    <x-step step="1" text="Register" icon="o-user">
                        Sign up or login if you already have an account
                    </x-step>
                    <x-step step="2" text="Shop" icon="o-shopping-cart">
                        Find the store closest to you and choose the gear you want to rent
                    </x-step>
                    <x-step step="3" text="Shop" icon="o-calendar">
                        Select the dates of your rental period.
                    </x-step>
                    <x-step step="4" text="Payment" icon="o-currency-dollar">
                        You can choose to pay with cash when picking up the gear or online trough Stripe.
                    </x-step>
                    <x-step step="5" text="Pick Up" icon="o-arrow-up-tray">
                        Pick up the gear at the store of your choice.
                    </x-step>
                    <x-step step="6" text="Camping" icon="o-map">
                        Have fun!
                    </x-step>
                    <x-step step="7" text="Return Gear" icon="o-arrow-down-tray">
                        Don't forge to return the gear at the store!
                    </x-step>
                </x-steps>

                <x-button icon="o-arrow-left-circle" class="mr-3" label="Previous" @click="if (step > 1) step--"
                    x-bind:disabled="step === 1" />

                <x-button icon="o-arrow-right-circle" class="btn-primary" label="Next" @click="if (step < 7) step++" x-bind:disabled="step === 7" />
            </div>

        </x-card>
    </div>

    <h1>Latest Gear</h1>
    <h1>Our Stores</h1>
    <h1>Your rentals</h1>
    <h1>Visit your Profile</h1>

</div>
