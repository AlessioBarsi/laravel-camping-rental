<?php

use Livewire\Volt\Component;
use App\Models\Article;
use App\Models\Category;
use App\Models\Store;
use App\Models\User;

new class extends Component {
    private $article_count = 0;
    private $user_count = 0;
    private $store_count = 0;
    private $category_count = 0;

    public function mount()
    {
        $this->article_count = Article::get()->count();
        $this->user_count = User::get()->count();
        $this->store_count = Store::get()->count();
        $this->category_count = Category::get()->count();
    }
}; ?>

<div>
    <h1 class="text-3xl font-bold">Dashbaord</h1>
    <div class="flex items-start my-3 space-x-5 p-5 rounded bg-primary">

        <x-stat title="Unique Articles" value="{{ $this->article_count }}" icon="o-cube" color="text-primary" />

        <x-stat title="Categories" value="{{ $this->category_count }}" icon="o-tag" />

        <x-stat title="Registered Users" value="{{ $this->user_count }}" icon="o-users" />

        <x-stat title="Stores" value="{{ $this->store_count }}" icon="o-building-storefront" color="text-green-500" />

    </div>


    <x-card title="Control Panel" separator class="p-2">
        <div class="flex flex-grow space-x-5">
            <x-button label="Manage Articles" class="flex-1 underline" link="/articles" />
            <x-button label="Manage Categories" class="flex-1 underline" link="/categories" />
            <x-button label="Manage Users" class="flex-1 underline" link="/users" />
            <x-button label="Manage Stores" class="flex-1 underline" link="/stores" />
        </div>
    </x-card>


    <div class="flex items-start text-2xl">Total revenue this month</div>

    <div class="flex items-start text-2xl">Due rentals</div>

    <div class="flex items-start text-2xl">Most active shops</div>

    <div class="flex items-start text-2xl">Latest uploaded articles</div>

    <div class="flex items-start text-2xl">Total Articles</div>

    <div class="flex items-start text-2xl">Settings - User Management</div>


</div>
