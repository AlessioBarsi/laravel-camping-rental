<?php

use Livewire\Volt\Component;
use App\Models\Article;

new class extends Component {
    public $slides = [];
    public function getArticlesProperty()
    {
        return Article::with(['images', 'category'])
            ->withCount('stores')
            ->latest()
            ->take(10)
            ->get();
    }

    public function showStoresDrawer($id)
    {
        $this->dispatch('show-stores-drawer', ['id' => $id]);
    }
}; ?>

<div>
    @if ($this->articles->count() > 0)
        @foreach ($this->articles as $article)
            @php
                $image_path =
                    $article->images->count() > 0
                        ? asset('storage/' . $article->images->first()->path)
                        : 'https://placehold.co/600x400';
                $this->slides[] = [
                    'image' => $image_path,
                    'title' => $article->title,
                    'description' => $article->description,
                    'stores_count' => $article->stores_count,
                    'id' => $article->id,
                    'category_name' => $article->category->name,
                    'category_id' => $article->category_id,
                ];
            @endphp
        @endforeach
    @endif

    <x-card title="Our Latest Gear" shadow separator>
        <x-carousel :slides="$this->slides" autoplay interval="3500">
            @scope('content', $slide)
                <div class="bg-primary/90 mt-10 w-[65%] p-5 mx-auto rounded-md">
                    <x-header title="{{ $slide['title'] }}" subtitle="Available at {{ $slide['stores_count'] }} stores"
                        separator>
                        <x-slot:actions>
                            <x-button icon="o-building-storefront" class="btn-sm bg-green-500 rounded-full"
                                tooltip="Browse Stores" wire:click="showStoresDrawer({{ $slide['id'] }})" />
                            <x-button icon="o-tag" class="btn-sm bg-purple-500 rounded-full"
                                tooltip="Category {{ $slide['category_name'] }}"
                                link="/shop/categories/{{ $slide['category_id'] }}" />
                            <x-button icon="o-document-magnifying-glass" class="btn-sm bg-blue-500 rounded-full"
                                tooltip="Article Page" link="/shop/articles/{{ $slide['id'] }}" />
                        </x-slot:actions>
                    </x-header>
                    {{ $slide['description'] }}
                </div>
            @endscope
        </x-carousel>
    </x-card>

    <livewire:shop.stores-drawer />
</div>
