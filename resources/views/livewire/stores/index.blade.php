<?php

use Livewire\Volt\Component;

use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

use App\Models\Store;
use App\Models\Image;
use Mary\Traits\Toast;

use Illuminate\Support\Facades\Storage;

new class extends Component {
    use Toast;
    use WithPagination;
    use WithFileUploads;

    public string $search = '';
    public bool $confirmModal = false;
    public bool $imageModal = false;
    public int $confirmDeleteID = 0;
    public int $imageId = 0;
    public int $uploadImageStoreID = 0;

    #[Rule('image|max:8000')]
    public $photo = null;

    public function openImageModal($id)
    {
        $this->reset('photo', 'imageId', 'uploadImageStoreID');
        $this->uploadImageStoreID = $id;
        $store = Store::findOrFail($id);
        $image = Image::where('imageable_type', Store::class)->where('imageable_id', $this->uploadImageStoreID)->first();

        if ($image && $image->id) {
            $this->imageId = $image->id;
        }

        $this->imageModal = true;
    }

    public function updatedPhoto()
    {
        //Delete old image(s) first if it exists
        $store = Store::findOrFail($this->uploadImageStoreID);

        foreach ($store->images as $image) {
            // Delete file from storage
            Storage::disk('public')->delete($image->path);
            // Delete DB record
            $image->delete();
        }   

        //Upload new image
        $path = $this->photo->store('images/stores', 'public');
        $store = Store::findOrFail($this->uploadImageStoreID);
        $new_image = $store->images()->create([
            'path' => $path,
        ]);
        //Update ImageId
        $this->imageId = $new_image->id;
        $this->reset('photo');
        $this->success('Image uploaded successfully');
    }

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

    public function dispatchStoreArticles($id)
    {
        $this->dispatch('store-articles', ['id' => $id]);
    }
}; ?>

<div>
    <div class="text-3xl font-bold my-2">Stores</div>
    <livewire:stores.form />
    <livewire:stores.articles />
    <div class="my-2">
        <x-input label="Search" wire:model.live.debounce.250ms="search" placeholder="Search store name or address" clearable />

        @foreach ($this->stores as $store)
            <x-list-item :item="$store" sub-value="address">
                <x-slot:actions>
                    <x-badge value="{{ $store->phone }}" class="badge-soft" />
                    <x-button icon="o-building-storefront" class="btn-sm bg-green-500"
                        wire:click="dispatchStoreArticles({{ $store->id }})" />
                    <x-button icon="o-trash" class="btn-sm bg-red-500"
                        wire:click="confirmDelete({{ $store->id }})" />
                    <x-button icon="o-pencil" class="btn-sm bg-blue-500"
                        wire:click="dispatchEdit({{ $store->id }})" />
                    <x-button icon="o-photo" class="btn-sm bg-blue-500"
                        wire:click="openImageModal({{ $store->id }})" />
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

        <x-modal :open="$imageModal" x-on:close="$wire.set('imageModal, false')" wire:model="imageModal" title="Image"
            class="backdrop-blur">

            <div wire:key="image-section-{{ $this->imageId }}">
                @if ($this->imageId === 0)
                    <x-alert title="No image found" description="No image has been uploaded for this store"
                        class="alert-warning" />
                @else
                    @php
                        $image = \App\Models\Image::find($this->imageId);
                    @endphp

                    @if ($image)
                        <img src="{{ asset('storage/' . $image->path) }}" class="object-cover rounded"
                            alt="Image {{ $image->path }} could not be loaded" />
                    @endif

                @endif
            </div>

            <div class="flex items-start space-x-1 my-2"></div>
            <x-file wire:model="photo" label="Upload Image" accept="image/*" />
            <div wire:loading wire:target="photo" class="text-blue-500" spinner>Uploading...</div>
            <x-slot:actions>
                <x-button label="Close" wire:click="$set('imageModal', false)" />
            </x-slot:actions>
        </x-modal>


    </div>
    {{ $this->stores->links() }}
</div>
