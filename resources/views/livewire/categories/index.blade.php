<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $confirmModal = false;
    public int $confirmDeleteID = 0;

    public function getCategoriesProperty()
    {
        return Category::when($this->search, function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
        })->paginate(5);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\On('category-update')]
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
                $category = Category::findOrFail($this->confirmDeleteID);
                $category->delete();

                $this->success("Category $category->name has been deleted");
            } catch (ModelNotFoundException $e) {
                $this->error('Category not found: ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->error('Error deleting category: ' . $e->getMessage());
            }
        } else {
            $this->error('Something went wrong. Please try again');
        }

        $this->confirmModal = false;
        $this->reset('confirmDeleteID');
    }

    public function dispatchEdit($id)
    {
        $this->dispatch('category-edit', ['id' => $id]);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2">Categories</div>
    <livewire:categories.form />
    <div class="my-2">
        <x-input label="Search" wire:model.live.debounce.250ms="search" placeholder="Search category name" clearable />

        @foreach ($this->categories as $category)
            <x-list-item :item="$category">
                <x-slot:actions>

                    <x-button icon="o-trash" class="btn-sm bg-red-500" wire:click="confirmDelete({{ $category->id }})" />
                    <x-button icon="o-pencil" class="btn-sm bg-blue-500" wire:click="dispatchEdit({{ $category->id }})" />
                </x-slot:actions>
            </x-list-item>
        @endforeach

        <x-modal :open="$confirmModal" x-on:close="$wire.set('confirmModal, false')" wire:model="confirmModal"
            title="Delete Category?" class="backdrop-blur">
            Articles associated to this category will be deleted as well
            <x-slot:actions>
                <x-button label="Confirm" class="bg-red-500 mx-2" wire:click="delete" spinner />
                <x-button label="Cancel" wire:click="$set('confirmModal', false)" />
            </x-slot:actions>
        </x-modal>

    </div>
    {{ $this->categories->links() }}
</div>
