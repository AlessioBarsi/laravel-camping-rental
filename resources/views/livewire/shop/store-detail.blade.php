<?php

use Livewire\Volt\Component;
use App\Models\Store;
use App\Models\Article;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithPagination;

    public $store = null;
    public string $search = '';

    public function getArticlesProperty()
    {
        if ($this->store != null && $this->store->id) {
            $storeId = $this->store->id;
            return Article::with(['stores', 'images', 'category'])
                ->when($storeId, function ($query, $storeId) {
                    $query->whereHas('stores', function ($q) use ($storeId) {
                        $q->where('stores.id', $storeId);
                    });
                })
                ->when($this->search, function ($query) {
                    $search = strtolower($this->search);
                    $query->where(function ($q) use ($search) {
                        $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
                    });
                })
                ->paginate(8);
        }
    }

    public function mount($id)
    {
        $this->store = Store::with(['articles', 'images'])->findOrFail($id);
    }

    public function showStoresDrawer($id)
    {
        $this->dispatch('show-stores-drawer', ['id' => $id]);
    }

    public function addToCart($id)
    {
        $this->dispatch('add-to-cart', ['storeId' => $this->store->id, 'articleId' => $id]);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2 text-center">{{ $store->name }}</div>

    @if ($this->articles->count() <= 0)
        <x-alert title="This store doesn't have any articles available at this moment" icon="o-exclamation-triangle"
            class="alert-warning my-2 w-[50%]" />
    @else
        <x-input label="Search" wire:model.live.debounce.500ms="search" placeholder="Search article title or description"
            clearable />
    @endif
    <div class="grid grid-cols-1 gap-4 my-3 md:grid-cols-2">
        @foreach ($this->articles as $article)
            <x-card key="{{ $article->id }}" class="w-full" separator title="{{ $article->title }}"
                subtitle="{{ $article->stores->count() > 1 ? 'Available at other stores' : 'Only available at this store' }}">

                @php
                    $store = $article->stores->firstWhere('id', $this->store->id);
                    $stock = $store->pivot->stock;
                @endphp

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

                <div>
                    {{ $article->description }}
                </div>

                <div class="my-2 w-[70%]">
                    @switch(true)
                        @case($stock <= 10)
                            <x-alert title="Only {{ $stock }} article(s) remaining" icon="o-exclamation-triangle"
                                class="alert-warning" />
                        @break

                        @case($stock <= 0)
                            <x-alert title="This article is out of order. Contact the store for more informations"
                                icon="o-no-symbol" class="alert-error" />
                        @break

                        @default
                            <x-alert title="{{ $stock }} of this article are available" icon="o-check"
                                class="alert-success" />
                    @endswitch

                </div>

                <x-slot:menu>
                    <x-button icon="o-tag" class="btn-primary btn-sm"
                        tooltip="Browse category {{ $article->category->name }}"
                        link="/shop/categories/{{ $article->category_id }}" />
                    <x-button icon="o-share" class="btn-secondary btn-sm" tooltip="View Article Page"
                        link="/shop/articles/{{ $article->id }}" />
                </x-slot:menu>

                <x-slot:actions>
                    <x-button label="Other Stores" icon="o-building-storefront" class="bg-green-500"
                        wire:click="showStoresDrawer({{ $article->id }})" :disabled="$article->stores->count() <= 1" spinner />
                    <x-button icon="o-shopping-cart" class="btn-primary" wire:click="addToCart({{ $article->id }})"
                        :disabled="$stock <= 0" spinner>
                        Add to Cart
                        <x-badge value="{{ $article->price }} $" class="badge-neutral badge-sm" />
                    </x-button>
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>

    <livewire:shop.stores-drawer />

    {{ $this->articles->links() }}
</div>
