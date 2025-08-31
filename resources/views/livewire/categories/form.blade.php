<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public bool $formModal = false;
    public int $editCategoryID = 0;

    #[Validate('required|min:3|max:50|unique:categories')]
    public $name = '';

    #[\Livewire\Attributes\On('category-edit')]
    public function editForm($payload)
    {
        $this->editCategoryID = $payload['id'];
        try {
            $category = Category::findOrFail($this->editCategoryID);
            $this->name = $category->name;
            $this->formModal = true;
        } catch (ModelNotFoundException $e) {
            $this->error('Category not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error editing category: ' . $e->getMessage());
        }
    }

    public function updatedFormModal($value)
    {
        if ($value === false) {
            $this->reset('name', 'editCategoryID');
            $this->clearValidation('name');
        }
    }

    public function submit()
    {
        $this->validate();

        if ($this->editCategoryID === 0) {
            try {
                Category::create([
                    'name' => $this->name,
                ]);
                $this->success('Category created');
                $this->dispatch('category-update');
            } catch (\Throwable $th) {
                $this->error('Something went wrong: ' . $th->getMessage());
            }
        } else {
            try {
                $category = Category::findOrFail($this->editCategoryID);
                $category->update([
                    'name' => $this->name,
                ]);
                $this->success("Category {$category->name} updated");
                $this->dispatch('category-update');
            } catch (ModelNotFoundException $e) {
                $this->error('Category not found: ' . $e->getMessage());
            } catch (\Throwable $th) {
                $this->error('Something went wrong: ' . $th->getMessage());
            }
        }

        $this->formModal = false;
        $this->reset('name', 'editCategoryID');
        $this->clearValidation('name');
    }
}; ?>

<div>

    <x-modal 
        :open="$formModal" 
        x-on:close="$wire.set('formModal', false)" 
        wire:model="formModal" title="{{ $editCategoryID != 0 ? 'Edit' : 'Add New' }} Category"
        class="backdrop-blur"
    >
        
        <x-form class="w-full" wire:submit="submit">
            <x-input label="Category Name" wire:model="name" />
            <x-slot:actions>
                <x-button label="Cancel" wire:click="$set('formModal', false)" />
                <x-button label="Save" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-button label="Add Category" icon="o-plus-circle" class="btn-success" wire:click="$set('formModal', true)" />

</div>
