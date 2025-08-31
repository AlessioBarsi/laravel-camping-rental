<?php

use Livewire\Volt\Component;
use App\Models\Store;
use Livewire\Attributes\Validate;
use Mary\Traits\Toast;

new class extends Component {
    use Toast;

    public bool $formModal = false;
    public int $editStoreID = 0;

    public $name = '';
    public $address = '';
    public $phone = '';

    public function rules()
    {
        return [
            'name' => 'required|min:3|max:50|unique:stores,name,' . $this->editStoreID,
            'address' => 'required|min:6|max:50',
            'phone' => 'required|min:9|max:20',
        ];
    }

    #[\Livewire\Attributes\On('store-edit')]
    public function editForm($payload)
    {
        $this->editStoreID = $payload['id'];
        try {
            $store = Store::findOrFail($this->editStoreID);
            $this->name = $store->name;
            $this->address = $store->address;
            $this->phone = $store->phone;
            $this->formModal = true;
        } catch (ModelNotFoundException $e) {
            $this->error('Store not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error editing store: ' . $e->getMessage());
        }
    }

    public function updatedFormModal($value)
    {
        if ($value === false) {
            $this->reset('name', 'address', 'phone', 'editStoreID');
            $this->clearValidation('name', 'address', 'phone');
        }
    }

    public function submit()
    {
        $this->validate();

        if ($this->editStoreID === 0) {
            try {
                Store::create([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                ]);
                $this->success('Store created');
                $this->dispatch('store-update');
            } catch (\Throwable $th) {
                $this->error('Something went wrong: ' . $th->getMessage());
            }
        } else {
            try {
                $store = Store::findOrFail($this->editStoreID);
                $store->update([
                    'name' => $this->name,
                    'address' => $this->address,
                    'phone' => $this->phone,
                ]);
                $this->success("Store {$store->name} updated");
                $this->dispatch('store-update');
            } catch (ModelNotFoundException $e) {
                $this->error('Store not found: ' . $e->getMessage());
            } catch (\Throwable $th) {
                $this->error('Something went wrong: ' . $th->getMessage());
            }
        }

        $this->formModal = false;
        $this->reset('name', 'editStoreID');
        $this->clearValidation('name');
    }
}; ?>

<div>

    <x-modal :open="$formModal" x-on:close="$wire.set('formModal', false)" wire:model="formModal"
        title="{{ $editStoreID != 0 ? 'Edit' : 'Add New' }} Store" class="backdrop-blur">

        <x-form class="w-full" wire:submit="submit">
            <div class="flex w-full">
                <x-input class="flex-1" label="Store Name" wire:model="name" />
                <x-input class="flex-1 mx-2" label="Phone" wire:model="phone" />
            </div>
            <x-input label="Address" wire:model="address" />
            <x-slot:actions>
                <x-button label="Cancel" wire:click="$set('formModal', false)" />
                <x-button label="Save" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    <x-button label="Add Store" icon="o-plus-circle" class="btn-success" wire:click="$set('formModal', true)" />

</div>
