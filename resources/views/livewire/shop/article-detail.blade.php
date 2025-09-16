<?php

use Livewire\Volt\Component;
use App\Models\Article;

new class extends Component {
    public $article = null;
    public function mount($id)
    {
        $this->article = Article::with(['images', 'stores'])->find($id);
    }
}; ?>

<div class="flex items-center">
    <x-card key="{{ $this->article->id }}" class="w-[65%]" separator title="{{ $this->article->title }}"
        subtitle="Available at {{ $this->article->stores->count() - 1 }} other stores">
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

        <x-slot:menu>
            <x-button icon="o-tag" class="btn-primary btn-sm" tooltip="Browse category {{ $this->article->category->name }}"
                link="/shop/categories/{{ $this->article->category_id }}" />
            <x-button icon="o-share" class="btn-secondary btn-sm" tooltip="View Article Page"
                link="/shop/articles/{{ $this->article->id }}" />
        </x-slot:menu>

        <x-slot:actions>
            <x-button label="Other Stores" icon="o-building-storefront" class="bg-green-500"
                livewire.click="openStoresDrawer({{ $this->article->id }})" />
            <x-button label="Add to Cart" icon="o-shopping-cart" class="btn-primary" link="/shop/categories" />
        </x-slot:actions>

    </x-card>
</div>
