<?php

use Livewire\Volt\Component;
use App\Models\Store;
use App\Models\ArticleInStore;
use App\Models\Article;
use Mary\Traits\Toast;

new class extends Component {
    public $storeId = null;
    public bool $showDrawer = false;
    public $selectedArticle = null;
    public int $articleAmount = 0;
    use Toast;

    public function getArticlesInStoreProperty()
    {
        if ($this->storeId) {
            return ArticleInStore::with('article')->where('store_id', $this->storeId)->get();
        }
        return collect();
    }

    public function getArticlesProperty()
    {
        return Article::all();
    }

    #[\Livewire\Attributes\On('store-articles')]
    public function showDrawer($payload)
    {
        $this->reset('articleAmount', 'selectedArticle');
        try {
            $store = Store::findOrFail($payload['id']);
            $this->storeId = $payload['id'];
            $this->showDrawer = true;
        } catch (ModelNotFoundException $e) {
            $this->error('Store not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function addArticle()
    {
        $articleId = (int) $this->selectedArticle;
        if ($articleId && $this->articleAmount > 0) {
            try {
                ArticleInStore::updateOrCreate(['article_id' => $articleId, 'store_id' => $this->storeId], ['stock' => $this->articleAmount]);
                $this->success('The article has been added to the store');
            } catch (\Throwable $th) {
                $this->error($th->getMessage());
            }
        } else {
            $this->error('You need to select an article and insert the amount');
        }
    }

    public function delete($id)
    {
        try {
            $articleInStore = ArticleInStore::findOrFail($id);
            $articleInStore->delete();

            $this->success('The article(s) has been removed from this store');
        } catch (ModelNotFoundException $e) {
            $this->error('Object not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error removing articles: ' . $e->getMessage());
        }
    }
}; ?>

<div>
    <x-drawer wire:model="showDrawer" class="w-11/12 lg:w-1/3" right>
        <div class="my-2">

            @if ($this->articlesInStore)

                <x-select class="flex-1" label="Category" wire:model="selectedArticle" :options="$this->articles" option-value="id"
                    option-label="title" placeholder="Select Article to Add" />
                <x-input class="flex-1" label="Amount" wire:model="articleAmount" type="number" />

                <x-button class="btn-primary my-2" label="Update" wire:click="addArticle" />
                <x-alert
                    title="To change the quantity of an existing article, select it from the dropdown menu and then select the new quantity you want to set"
                    icon="o-information-circle" class="alert-info alert-soft my-2" />

                @if ($this->articlesInStore->count() > 0)
                    @foreach ($this->articlesInStore as $articleInStore)
                        <x-list-item :item="$articleInStore">
                            <x-slot:value>
                                {{ $articleInStore->article->title }}
                            </x-slot:value>
                            <x-slot:sub-value>
                                Quantity in stock: {{ $articleInStore->stock }}
                            </x-slot:sub-value>
                            <x-slot:actions>
                                <x-button label="Remove Article" class="bg-red-500"
                                    wire:click="delete({{ $articleInStore->id }})" />
                            </x-slot:actions>
                        </x-list-item>
                    @endforeach
                @else
                    There are no articles assigned to this store.
                @endif
            @else
                Something went wrong while fetching articles. Please try again.
            @endif
        </div>
        <x-button label="Close" @click="$wire.showDrawer = false" />
        <x-button class="bg-green-500 mx-2" label="View Store Page" link="/stores" />
    </x-drawer>
</div>
