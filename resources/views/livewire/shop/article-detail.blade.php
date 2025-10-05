<?php

use Livewire\Volt\Component;
use App\Models\Article;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    public $article = null;

    public function mount($id)
    {
        $this->article = Article::with(['images', 'stores'])->find($id);
    }

    public function showStoresDrawer($id)
    {
        $this->dispatch('show-stores-drawer', ['id' => $id]);
    }
}; ?>

<div class="flex justify-center">
    <x-card key="{{ $this->article->id }}" class="w-full md:w-[65%]" separator title="{{ $this->article->title }}"
        subtitle="Available at {{ $this->article->stores->count() }} stores">
        @if ($this->article->images->count() > 0)
            @php
                $slides = $this->article->images
                    ->map(function ($image) {
                        return [
                            'image' => asset('storage/' . $image->path),
                        ];
                    })
                    ->toArray();
            @endphp
            <x-carousel :slides="$slides" autoplay interval="2500" class="mb-2" />
        @endif

        {{ $this->article->description }}

        @if ($this->article->stores->count() <= 0)
            <x-alert title="This article has yet to arrive to our stores" icon="o-exclamation-triangle" class="my-2" />
        @endif
        <x-slot:menu>
            <x-button icon="o-tag" class="btn-primary btn-sm"
                tooltip="Browse Category {{ $this->article->category->name }}"
                link="/shop/categories/{{ $this->article->category_id }}" />
        </x-slot:menu>

        <x-slot:actions>
            <x-button icon="o-building-storefront" class="bg-green-500"
                wire:click="showStoresDrawer({{ $this->article->id }})" :disabled="$article->stores->count() <= 0" spinner>
                Browse Stores
                <x-badge value="{{ $this->article->price }} $" class="badge-neutral badge-sm" />
            </x-button>
        </x-slot:actions>
    </x-card>

    <livewire:shop.stores-drawer />
</div>
