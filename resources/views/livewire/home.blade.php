<?php

use Livewire\Volt\Component;
use App\Models\Category;
use App\Models\Article;
use App\Models\Store;

new class extends Component {
    public $step = 1;
    private $article_count = 0;
    private $user_count = 0;
    private $store_count = 0;
    private $category_count = 0;

    public function mount()
    {
        $this->article_count = Article::get()->count();
        $this->store_count = Store::get()->count();
        $this->category_count = Category::get()->count();
    }
}; ?>

<div>
    <div class="text-5xl font-bold my-2 w-auto text-center">Laravel Camping Rental</div>
    <div class="flex items-start space-x-5 my-3">

        <x-card class="w-[30%]" title="Rent Camping Gear Easy"
            subtitle="Affordable, high-quality equipment for your next adventure" shadow separator>
            <x-button label="Browse Articles" class="btn-primary" link="/shop/articles" />
            <x-button label="Sign Up" class="mx-3 underline" link="/register" />
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

                <x-button icon="o-arrow-right-circle" class="btn-primary" label="Next" @click="if (step < 7) step++"
                    x-bind:disabled="step === 7" />
            </div>

        </x-card>
    </div>

    <livewire:shop.articles-carousel />

    <x-card title="Discover our Stores" subtitle="Find the best gear for your camping" shadow separator class="my-3">
        <x-slot:menu>
            <x-button label="Browse Stores" class="btn-primary" link="/shop/stores" />
            <x-button label="Brose Categories" class="btn-secondary" link="/shop/categories" />
        </x-slot:menu>

        <div class="flex items-start my-3 space-x-5 p-5 rounded bg-primary">

            <x-stat title="Unique Articles" value="{{ $this->article_count }}" icon="o-cube" color="text-primary" />

            <x-stat title="Categories" value="{{ $this->category_count }}" icon="o-tag" />

            <x-stat title="Stores" value="{{ $this->store_count }}" icon="o-building-storefront"
                color="text-green-500" />

        </div>
    </x-card>

    <div class="flex items-start space-x-3 my-3">
        <x-card class="w-[50%]" title="Your rentals" subtitle="The articles you've currently or previously rented" shadow separator>
            Login to see your rentals
            <x-slot:actions separator>
                <x-button label="Login" class="btn-primary" />
            </x-slot:actions>
        </x-card>

        <x-card class="w-[50%]" title="Your profile" subtitle="Our findings about you" shadow separator>
            Login to manage your profile
            <x-slot:actions separator>
                <x-button label="Login" class="btn-primary" />
            </x-slot:actions>
        </x-card>
    </div>

</div>
