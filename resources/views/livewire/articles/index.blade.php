<?php

use Livewire\Volt\Component;
use App\Models\Article;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $confirmModal = false;
    public int $confirmDeleteID = 0;

    public function getArticlesProperty()
    {
        return Article::with('category') // eager load category
            ->when($this->search, function ($query) {
                $search = strtolower($this->search);
                $query->where(function ($q) use ($search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(description) LIKE ?', ["%{$search}%"]);
                });
            })
            ->paginate(5);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\On('article-update')]
    public function refreshList()
    {
        // re-render the component
    }

    public function confirmDelete($id)
    {
        $this->confirmDeleteID = $id;
        $this->confirmModal = true;
    }

    public function delete()
    {
        if ($this->confirmDeleteID != 0) {
            try {
                $article = Article::findOrFail($this->confirmDeleteID);
                $article->delete();

                $this->success("Article $article->title has been deleted");
            } catch (ModelNotFoundException $e) {
                $this->error('Article not found: ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->error('Error deleting article: ' . $e->getMessage());
            }
        } else {
            $this->error('Something went wrong. Please try again');
        }

        $this->confirmModal = false;
        $this->reset('confirmDeleteID');
    }

    public function dispatchEdit($id)
    {
        $this->dispatch('article-edit', ['id' => $id]);
    }

    public function dispatchImagesDrawer($id)
    {
        $this->dispatch('article-images', ['id' => $id]);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2">Articles</div>
    <livewire:articles.form />
    <livewire:articles.images />
    <div class="my-2">
        <x-input label="Search" wire:model.live.debounce.250ms="search" placeholder="Search article name or description"
            clearable />

        @foreach ($this->articles as $article)
            <x-list-item :item="$article">
                <x-slot:actions>
                    <x-slot:value>
                        {{ $article->title }}
                    </x-slot:value>
                    <x-slot:sub-value>
                        {{ $article->description }}
                    </x-slot:sub-value>
                    <x-slot:avatar>
                        <x-badge value="{{ $article->category->name }}" class="badge-primary" />
                        <x-badge value="{{ $article->price }}" class="badge-soft mx-2" />
                    </x-slot:avatar>
                    <x-button icon="o-trash" class="btn-sm bg-red-500" wire:click="confirmDelete({{ $article->id }})" />
                    <x-button icon="o-pencil" class="btn-sm bg-blue-500"
                        wire:click="dispatchEdit({{ $article->id }})" />
                    <x-button icon="o-photo" class="btn-sm bg-green-500" wire:click="dispatchImagesDrawer({{ $article->id }})" />
                </x-slot:actions>
            </x-list-item>
        @endforeach

        <x-modal wire:model="confirmModal" title="Delete Article?" class="backdrop-blur">
            The article will be removed from every store
            <x-slot:actions>
                <x-button label="Confirm" class="bg-red-500 mx-2" wire:click="delete()" spinner />
                <x-button label="Cancel" wire:click="$set('confirmModal', false)" />
            </x-slot:actions>
        </x-modal>

    </div>

    {{ $this->articles->links() }}
</div>
