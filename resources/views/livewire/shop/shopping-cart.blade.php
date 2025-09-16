<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Models\ArticleInStore;

new class extends Component {
    use Toast;
    public bool $cartDrawer = false;
    public int $articleId = 0;
    public int $storeId = 0;
    public $articleInStore = null;

    #[\Livewire\Attributes\On('add-to-cart')]
    public function showDrawer($payload)
    {
        $this->reset('articleId', 'storeId', 'articleInStore');
        try {
            $this->articleId = $payload['articleId'];
            $this->storeId = $payload['storeId'];
            $this->cartDrawer = true;

            $this->articleInStore = ArticleInStore::where('article_id', $this->articleId)->where('store_id', $this->storeId)->first();
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function remove($id) {
        $this->success("Item removed from your cart with id#".$id);
    }
}; ?>

<div>
    <x-drawer wire:model="cartDrawer" class="w-11/12 lg:w-1/3" right>
        <div>


            @if ($this->articleInStore !== null)
            @php
                $image_url = 'Images not found';
                if ($this->articleInStore->article->images->count() > 0) {
                    $image_url = $this->articleInStore->article->images->first()->path;
                }

            @endphp
                <x-list-item :item="$this->articleInStore">
                    <x-slot:avatar>
                            <x-avatar :image="asset('storage/' . $image_url)" alt="{{ $image_url }}" />

                    </x-slot:avatar>
                    <x-slot:value>
                        {{ $this->articleInStore->article->title }}
                    </x-slot:value>
                    <x-slot:sub-value>
                        From store {{ $this->articleInStore->store->name }}
                    </x-slot:sub-value>
                    <x-slot:actions>
                        <x-button icon="o-trash" class="btn-sm" wire:click="remove({{ $this->articleInStore->id }})" spinner />
                    </x-slot:actions>
                </x-list-item>
            @endif
        </div>
        <div class="flex items-end my-2 space-x-5">
            <x-button label="Close" @click="$wire.cartDrawer = false" />
            <x-button label="Check Out" class="btn-primary" wire:click="checkOut" spinner />
        </div>
    </x-drawer>
</div>
