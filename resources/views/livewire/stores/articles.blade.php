<?php

use Livewire\Volt\Component;
use App\Models\Store;
use App\Models\ArticleInStore;
use Mary\Traits\Toast;

new class extends Component {
    public $storeID = null;
    public bool $showDrawer = false;
    use Toast;

    public function getArticlesProperty(){
        if ($this->storeID) {
            return ArticleInStore::where('store_id', $this->storeID)->get();
        }
        return collect();
    }

    #[\Livewire\Attributes\On('store-articles')]
    public function showDrawer($payload)
    {
        try {
            $store = Store::findOrFail($payload['id']);
            $this->storeID = $payload['id'];
            $this->showDrawer = true;
        } catch (ModelNotFoundException $e) {
            $this->error('Store not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <x-drawer wire:model="showDrawer" class="w-11/12 lg:w-1/3" right>
        <div>@if ($this->articles)
            Articles here
            @if ($this->articles.length > 0)
                
            @else
                
            @endif
        @else
            Something went wrong while fetching articles. Please try again.
        @endif</div>
        <x-button label="Close" @click="$wire.showDrawer = false" />
    </x-drawer>

    <x-button label="Open Right" wire:click="$toggle('showDrawer')" />
</div>
