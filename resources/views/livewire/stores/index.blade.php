<?php

use Livewire\Volt\Component;
use App\Models\Store;
use Livewire\Attributes\Validate;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;
    use WithPagination;

    public string $search = '';
    public bool $confirmModal = false;
    public int $confirmDeleteID = 0;

    public function getStoresProperty()
    {
        return Store::when($this->search, function ($query) {
            $search = strtolower($this->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"])->orWhereRaw('LOWER(address) LIKE ?', ["%{$search}%"]);
            });
        })->paginate(5);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[\Livewire\Attributes\On('store-update')]
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
                $store = Store::findOrFail($this->confirmDeleteID);
                $store->delete();

                $this->success("Store $store->name has been deleted");
            } catch (ModelNotFoundException $e) {
                $this->error('Store not found: ' . $e->getMessage());
            } catch (\Exception $e) {
                $this->error('Error deleting store: ' . $e->getMessage());
            }
        } else {
            $this->error('Something went wrong. Please try again');
        }

        $this->confirmModal = false;
        $this->reset('confirmDeleteID');
    }

    public function dispatchEdit($id)
    {
        $this->dispatch('store-edit', ['id' => $id]);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2">Stores</div>
    <livewire:stores.form />
    <div class="my-2">
        <x-input label="Clearable" wire:model.live.debounce.250ms="search" placeholder="Search store" clearable />

        @foreach ($this->stores as $store)
            <x-list-item :item="$store" sub-value="address">
                <x-slot:actions>

                    <x-button icon="o-trash" class="btn-sm bg-red-500" wire:click="confirmDelete({{ $store->id }})" />
                    <x-button icon="o-pencil" class="btn-sm bg-blue-500" wire:click="dispatchEdit({{ $store->id }})" />
                </x-slot:actions>
            </x-list-item>
        @endforeach

        <x-modal :open="$confirmModal" x-on:close="$wire.set('confirmModal, false')" wire:model="confirmModal"
            title="Delete Store?" class="backdrop-blur">
            <x-slot:actions>
                <x-button label="Confirm" class="bg-red-500 mx-2" wire:click="delete" spinner />
                <x-button label="Cancel" wire:click="$set('confirmModal', false)" />
            </x-slot:actions>
        </x-modal>

    </div>
    {{ $this->stores->links() }}
</div>
