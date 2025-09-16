<?php

use Livewire\Volt\Component;
use App\Models\Store;

new class extends Component {
    public string $search = '';
    public function getStoresProperty()
    {
        return Store::with(['articles', 'images'])
            ->when($this->search, function ($query) {
                $search = strtolower($this->search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"]);
                });
            })
            ->get();
    }
}; ?>

<div>
    <div class="text-3xl font-bold">Discover Our Stores</div>

    <x-input label="Search" wire:model.live.debounce.500ms="search" placeholder="Search store name or address" clearable />

    <div class="grid grid-cols-3 gap-4 my-3">
        @foreach ($this->stores as $store)
            <x-card class="w-full" key="{{ $store->id }}" title="{{ $store->name }}"
                subtitle="{{ $store->articles->count() }} unique articles available in this store" shadow separator>
                <x-slot:figure>
                    @if ($store->images->count() > 0)
                        <img src="{{ asset('storage/' . $store->images->first()->path) }}" class="fill">
                    @else
                        <img src="https://placehold.co/600x400/" class="fill" />
                    @endif
                </x-slot:figure>
                <x-slot:menu>
                    <x-button icon="o-map-pin" class="btn-secondary btn-sm" tooltip="View Location"
                        link="https://www.google.com/maps/search/?api=1&query={{ urlencode($store->address) }}" />
                    <x-button icon="o-phone" class="btn-primary btn-sm" link="tel:{{ $store->phone }}" />
                </x-slot:menu>
                {{ $store->address }}
                <x-slot:actions separator>
                    <x-button label="Browse Articles" icon="o-shopping-cart" class="bg-green-500"
                        link="/shop/stores/{{ $store->id }}" />
                </x-slot:actions>
            </x-card>
        @endforeach
    </div>
    
</div>
