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
    public $storesForArticle = null;
    public bool $storesDrawer = false;

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

    public function openStoresDrawer($id)
    {
        $this->storesForArticle = null;
        $article = $article = $this->articles->where('id', $id)->first();
        $this->storesForArticle = $article->stores;
        $this->storesDrawer = true;
    }

    public function addToCart($id)
    {
        $this->dispatch('add-to-cart', ['storeId' => $this->store->id , 'articleId' => $id]);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2 text-center">Articles available at {{ $store->name }}</div>

    <div class="grid grid-cols-2 gap-4 my-3">
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
                            <x-alert title="Only {{ $stock }} article(s) remaining" icon="o-exclamation-triangle" class="alert-warning" />
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
                        wire:click="openStoresDrawer({{ $article->id }})" :disabled="$article->stores->count() <= 1" spinner />
                    <x-button label="Add to Cart" icon="o-shopping-cart" class="btn-primary"
                        wire:click="addToCart({{ $article->id }})" :disabled="$stock <= 0" spinner />
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>

    <x-drawer wire:model="storesDrawer" class="w-11/12 lg:w-1/3">
        @if ($this->storesForArticle !== null)
            @foreach ($this->storesForArticle as $store)
                <x-list-item :item="$store">
                    <x-slot:value>
                        {{ $store->name }}
                    </x-slot:value>
                    <x-slot:sub-value class="my-1 mx-2">
                        <x-badge value="{{ $store->pivot->stock }} in stock" class="badge-primary mx-2" />
                        {{ $store->address }}
                    </x-slot:sub-value>
                    <x-slot:actions>
                        <x-button icon="o-paper-airplane" class="btn-sm bg-green-500" tooltip="Visit Store Page"
                            link="/shop/stores/{{ $store->id }}" />
                        <x-button icon="o-map-pin" class="btn-secondary btn-sm"
                            link="https://www.google.com/maps/search/?api=1&query={{ urlencode($store->address) }}"
                            tooltip="See position" />
                        <x-button icon="o-phone" class="btn-primary btn-sm" link="tel:{{ $store->phone }}"
                            tooltip="Call Store" />
                    </x-slot:actions>
                </x-list-item>
            @endforeach
        @else
            <div>
                <x-alert title="Stores could not be loaded"
                    description="An error occurred while loading stores. Please refresh the page and try again"
                    icon="o-exclamation-triangle" class="alert-warning" />
            </div>
        @endif

        <x-button class="my-2" label="Close" @click="$wire.storesDrawer = false" />
    </x-drawer>

    {{ $this->articles->links() }}
</div>
