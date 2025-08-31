<?php

use Livewire\Volt\Component;
use App\Models\Article;
use App\Models\Category;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public $title = '';

    public $description = '';

    public $category = null;

    public float $price = 0.0;

    public bool $formModal = false;
    public int $editArticleID = 0;

    public function rules()
    {
        return [
            'title' => 'required|min:3|max:50|unique:articles,title,' . $this->editArticleID,
            'description' => 'required|min:3|max:400',
            'category' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:1|max:100000',
        ];
    }

    public function getCategoriesProperty()
    {
        return Category::select('id', 'name')->get()->toArray();
    }

    #[\Livewire\Attributes\On('article-edit')]
    public function editForm($payload)
    {
        $this->editArticleID = $payload['id'];
        try {
            $article = Article::findOrFail($this->editArticleID);
            $this->title = $article->title;
            $this->description = $article->description;
            $this->category = $article->category_id;
            $this->price = $article->price;
            $this->formModal = true;
        } catch (ModelNotFoundException $e) {
            $this->error('Article not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error editing article: ' . $e->getMessage());
        }
    }

    public function updatedFormModal($value)
    {
        if ($value === false) {
            $this->reset('title', 'description', 'category', 'price', 'editArticleID');
            $this->clearValidation('title', 'description', 'category', 'price');
        }
    }

    public function submit()
    {
        $this->validate();

        if ($this->editArticleID === 0) {
            try {
                Article::create([
                    'title' => $this->title,
                    'description' => $this->description,
                    'category_id' => (int) $this->category,
                    'price' => $this->price,
                ]);
                $this->success('Article created');
                $this->dispatch('article-update');
            } catch (\Throwable $th) {
                $this->error($th->getMessage());
            }
        } else {
            try {
                $article = Article::findOrFail($this->editArticleID);
                $article->update([
                    'title' => $this->title,
                    'description' => $this->description,
                    'category_id' => (int) $this->category,
                    'price' => $this->price,
                ]);
                $this->success("Article {$article->title} updated");
                $this->dispatch('article-update');
            } catch (ModelNotFoundException $e) {
                $this->error('Article not found: ' . $e->getMessage());
            } catch (\Throwable $th) {
                $this->error('Something went wrong: ' . $th->getMessage());
            }
        }

        $this->formModal = false;
        $this->reset('title', 'description', 'category', 'price');
        $this->clearValidation('title', 'description', 'category', 'price');
    }
}; ?>

<div>

    <x-modal :open="$formModal" x-on:close="$wire.set('formModal', false)" wire:model="formModal"
        title="{{ $editArticleID != 0 ? 'Edit' : 'Add New' }} Article" class="backdrop-blur">
        <x-form wire:submit="submit">
            <x-input label="Title" wire:model="title" />
            <x-textarea label="Description" wire:model="description" hint="400 characters limit" rows="5" />
            <div class="flex w-full gap-4">
                <x-select class="flex-1" label="Category" wire:model="category" :options="$this->categories" option-value="id"
                    option-label="name" placeholder="Select Category" />
                <x-input class="flex-1" label="Price" wire:model="price" type="number" />
            </div>

            <x-slot:actions>
                <x-button label="Cancel" wire:click="$set('formModal', false)" />
                <x-button label="Save" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-button label="Add Article" icon="o-plus-circle" class="btn-success" wire:click="$set('formModal', true)" />

</div>
