<?php

use Livewire\Volt\Component;
use App\Models\Article;

new class extends Component {
    public bool $storesDrawer = false;
    public int $articleId = 0;

    public function getStoresProperty()
    {
        if ($this->articleId !== 0) {
            $article = Article::with(['stores' => function ($q) {
            $q->withPivot('stock');
            }])->findOrFail($this->articleId);

            return $article->stores;
        }
    }

    #[\Livewire\Attributes\On('show-stores-drawer')]
    public function showStoreDrawer($payload) {
        $this->reset('articleId');
        $this->storesDrawer = true;
        $this->articleId = $payload['id'];
    }

}; ?>

<div>
    <x-drawer wire:model="storesDrawer" class="w-11/12 lg:w-1/3">
        @if ($this->stores !== null && $this->stores->count() > 0)
            @foreach ($this->stores as $store)
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
</div>
