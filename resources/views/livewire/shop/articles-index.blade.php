<?php

use Livewire\Volt\Component;
use App\Models\Article;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $selectedCategory = '';
    public float $minPrice = 0;
    public float $maxPrice = 0;

    public function getArticlesProperty()
    {
        return Article::with(['stores', 'images', 'category'])
            ->when($this->search, function ($query) {
                $search = strtolower($this->search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
                });
            })
            ->paginate(10);
    }

    public function getCategoriesProperty()
    {
        return Category::has('articles')->get();
    }

    public function showStoresDrawer($id)
    {
        $this->dispatch('show-stores-drawer', ['id' => $id]);
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->minPrice = 0;
        $this->maxPrice = 0;
    }
}; ?>

<div>
    <div class="text-3xl font-bold text-center">Browse our Articles</div>

    <x-input label="Search" wire:model.live.debounce.500ms="search" placeholder="Search article title or description"
        clearable />

    <div class="flex items-start space-x-3 my-2">
        <x-button class="mt-9 w-[25%]" icon="o-x-mark" label="Reset filters" wire:click="resetFilters" />
        <x-select class="w-[25%]" label="Category" wire:model="selectedCategory" :options="$this->categories" icon="o-user" />
        <x-input class="w-[25%]" label="Minimum Price" wire:model="minPrice" prefix="USD" />
        <x-input class="w-[25%]" label="Maximum Price" wire:model="maxPrice" prefix="USD" />
    </div>

    <div class="grid grid-cols-1 gap-4 my-3 md:grid-cols-3">
        @foreach ($this->articles as $article)
            <x-card key="{{ $article->id }}" class="w-full" separator title="{{ $article->title }}"
                subtitle="Available at {{ $article->stores->count() }} stores">
                @if ($article->images->count() > 0)
                    @php
                        $slides = $article->images
                            ->map(function ($image) {
                                return [
                                    'image' => asset('storage/' . $image->path),
                                ];
                            })
                            ->toArray();
                    @endphp
                    <x-carousel :slides="$slides" autoplay interval="2500" class="mb-2" />
                @endif

                {{ $article->description }}

                @if ($article->stores->count() <= 0)
                    <x-alert title="This article has yet to arrive to our stores" icon="o-exclamation-triangle"
                        class="my-2" />
                @endif
                <x-slot:menu>
                    <x-button icon="o-tag" class="btn-primary btn-sm"
                        tooltip="Browse category {{ $article->category->name }}"
                        link="/shop/categories/{{ $article->category_id }}" />
                    <x-button icon="o-share" class="btn-secondary btn-sm" tooltip="View Article Page"
                        link="/shop/articles/{{ $article->id }}" />
                </x-slot:menu>

                <x-slot:actions>
                    <x-button icon="o-building-storefront" class="bg-green-500"
                        wire:click="showStoresDrawer({{ $article->id }})" :disabled="$article->stores->count() <= 0" spinner>
                        Browse Stores
                        <x-badge value="{{ $article->price }} $" class="badge-neutral badge-sm" />
                    </x-button>
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>
    <livewire:shop.stores-drawer />

    {{ $this->articles->links() }}
</div>
