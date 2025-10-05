<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Models\ArticleInStore;

new class extends Component {
    use Toast;
    public $shoppingCart = [];
    public bool $cartDrawer = false;
    public float $totalValue = 0.0;

    public function mount()
    {
        $this->shoppingCart = session('shoppingCart', []);
        $this->totalValue = 0.0;
    }

    public function getCartItemsProperty()
    {
        // Extract only item IDs from the shoppingCart array
        $itemIds = collect($this->shoppingCart)->pluck('item')->toArray();

        return ArticleInStore::with(['article.images', 'store'])
            ->whereIn('id', $itemIds)
            ->get()
            ->map(function ($articleInStore) {
            // Attach the quantity from the shopping cart
            $quantity = collect($this->shoppingCart)
                ->firstWhere('item', $articleInStore->id)['quantity'] ?? 1;
            $articleInStore->quantity = $quantity;
            $this->totalValue += $quantity*$articleInStore->article->price;
            return $articleInStore;
        });
    }

    #[\Livewire\Attributes\On('add-to-cart')]
    public function showDrawer($payload)
    {
        try {
            $articleId = $payload['articleId'];
            $storeId = $payload['storeId'];
            $this->cartDrawer = true;
            //Get record of selected article from pivot table
            $articleInStore = ArticleInStore::where('article_id', $articleId)->where('store_id', $storeId)->first();
            //Check if article already added to cart
            $itemIndex = collect($this->shoppingCart)->search(fn($cartItem) => $cartItem['item'] === $articleInStore->id);
            if ($itemIndex === false) {
                //Create new entry with id and quantity
                $this->shoppingCart[] = [
                    'item' => $articleInStore->id,
                    'quantity' => 1,
                ];
                $this->success('Article added to the cart');
            } else {
                //Update quantity otherwise
                $this->shoppingCart[$itemIndex]['quantity']++;
                $this->success('The cart has been updated');
            }
            //Save the cart to session
            session()->put('shoppingCart', $this->shoppingCart);
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
        //Remove the entry from the array
        $this->shoppingCart = array_filter($this->shoppingCart, fn($cartItem) => $cartItem['item'] != $articleInStoreId);
        //Reindex the array
        $this->shoppingCart = array_values($this->shoppingCart);
        //Sync array to session
        session()->put('shoppingCart', $this->shoppingCart);
        $this->success('Article has been removed from the cart');
    }

    public function clearCart()
    {
        $this->shoppingCart = [];
        session()->forget('shoppingCart');
        $this->success('The cart has been emptied');
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
                        <x-badge value="{{ $articleInCart->article->price*$articleInCart->quantity }} $" class="badge-neutral badge-sm" />
                    </x-slot:value>
                    <x-slot:sub-value>
                        From store {{ $articleInCart->store->name }}
                    </x-slot:sub-value>
                    <x-slot:actions>
                        <x-badge class="mt-1" value="x{{ $articleInCart->quantity }}" class="badge-primary badge-sm" />
                        <x-button icon="o-trash" class="btn-sm" wire:click="removeFromCart({{ $articleInCart->id }})"
                            spinner />
                    </x-slot:actions>
                </x-list-item>
            @endforeach

        </div>
        <div class="flex items-end my-4 space-x-5">
            <x-button class="btn-primary" wire:click="checkOut" spinner>
                Check Out
                <x-badge value="{{ $this->totalValue }} $" class="badge-neutral badge-sm" />
            </x-button>
            <x-button label="Empty Cart" class="btn-secondary" wire:click="clearCart" spinner />
            <x-button label="Close" @click="$wire.cartDrawer = false" />
        </div>
    </x-drawer>
</div>
