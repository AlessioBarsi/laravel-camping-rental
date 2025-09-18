<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Models\ArticleInStore;

new class extends Component {
    use Toast;
    public $shoppingCart = [];
    public bool $cartDrawer = false;

    public function mount()
    {
        $this->shoppingCart = session('shoppingCart', []);
    }

    public function getCartItemsProperty()
    {
        return ArticleInStore::with(['article.images', 'store'])
            ->whereIn('id', $this->shoppingCart)
            ->get();
    }

    #[\Livewire\Attributes\On('add-to-cart')]
    public function showDrawer($payload)
    {
        try {
            $articleId = $payload['articleId'];
            $storeId = $payload['storeId'];
            $this->cartDrawer = true;

            $articleInStore = ArticleInStore::where('article_id', $articleId)->where('store_id', $storeId)->first();

            if (!in_array($articleInStore->id, $this->shoppingCart)) {
                $this->shoppingCart[] = $articleInStore->id;
                session()->put('shoppingCart', $this->shoppingCart);
                $this->success('Article added to your cart');
            } else {
                $this->warning('Article is already in your cart');
            }
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    #[\Livewire\Attributes\On('toggle-cart')]
    public function toggleCart()
    {
        $this->cartDrawer = !$this->cartDrawer;
    }

    public function removeFromCart($articleInStoreId)
    {
        $this->shoppingCart = array_filter($this->shoppingCart, fn($id) => $id != $articleInStoreId);
        $this->shoppingCart = array_values($this->shoppingCart);
        session()->put('shoppingCart', $this->shoppingCart);
        $this->success('Article has been removed from your cart');
    }

    public function clearCart()
    {
        $this->shoppingCart = [];
        session()->forget('shoppingCart');
        $this->success('Your cart has been emptied');
    }

    public function checkOut()
    {
        //WIP
    }
}; ?>

<div>

    <x-drawer wire:model="cartDrawer" class="w-11/12 lg:w-1/3" right>
        <div>
            @if ($this->cartItems->count() > 0)
                <div class="text-3xl font-bold">You have {{ $this->cartItems->count() }} articles in your cart</div>
            @else
                <x-alert title="Your cart is empty" icon="o-shopping-cart" class="alert-info alert-outline" />
            @endif
            @foreach ($this->cartItems as $articleInCart)
                @php
                    $image_url = 'Images not found';
                    if ($articleInCart->article->images->count() > 0) {
                        $image_url = $articleInCart->article->images->first()->path;
                    }

                @endphp

                <x-list-item :item="$articleInCart">
                    <x-slot:avatar>
                        <x-avatar :image="asset('storage/' . $image_url)" alt="{{ $image_url }}" />

                    </x-slot:avatar>
                    <x-slot:value>
                        {{ $articleInCart->article->title }}
                    </x-slot:value>
                    <x-slot:sub-value>
                        From store {{ $articleInCart->store->name }}
                    </x-slot:sub-value>
                    <x-slot:actions>
                        <x-button icon="o-trash" class="btn-sm" wire:click="removeFromCart({{ $articleInCart->id }})"
                            spinner />
                    </x-slot:actions>
                </x-list-item>
            @endforeach

        </div>
        <div class="flex items-end my-2 space-x-5">
            <x-button label="Close" @click="$wire.cartDrawer = false" />
            <x-button label="Check Out" class="btn-primary" wire:click="checkOut" spinner />
            <x-button label="Empty Cart" class="btn-secondary" wire:click="clearCart" spinner />
        </div>
    </x-drawer>
</div>
