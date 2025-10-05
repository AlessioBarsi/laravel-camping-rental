<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';

    public function getCategoriesProperty()
    {
        return Category::withCount('articles')
            ->has('articles')
            ->when($this->search, function ($query) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($this->search) . '%']);
        })->paginate(10);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2">Categories</div>
    <div class="my-2">
        <x-input label="Search" wire:model.live.debounce.250ms="search" placeholder="Search category" clearable />

        @foreach ($this->categories as $category)
            <x-list-item :item="$category">
                <x-slot:value>
                    {{ $category->name }}
                </x-slot:value>
                <x-slot:sub-value>
                    {{ $category->articles_count }} Articles associated to this category
                </x-slot:sub-value>
                <x-slot:actions>
                    <x-button label="Browse Articles" class="btn-sm btn-primary"
                        link="/shop/articles&category={{ $category->name }}" />
                </x-slot:actions>
            </x-list-item>
        @endforeach

    </div>
    {{ $this->categories->links() }}
</div>
