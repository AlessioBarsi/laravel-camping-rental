<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;

use App\Models\Article;
use App\Models\Image;
use Mary\Traits\Toast;

use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;
    use Toast;
    public bool $showDrawer = false;
    public int $articleId = 0;

    #[Rule('image|max:8000')]
    public $photo = null;

    #[\Livewire\Attributes\On('article-images')]
    public function showDrawer($payload)
    {
        $this->reset('articleId');
        $this->reset('photo');
        try {
            $article = Article::findOrFail($payload['id']);
            $this->articleId = $payload['id'];
            $this->showDrawer = true;
        } catch (ModelNotFoundException $e) {
            $this->error('Article not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }

    public function getImagesProperty()
    {
        return Image::where('imageable_type', Article::class)->where('imageable_id', $this->articleId)->get();
    }

    public function updatedPhoto()
    {
        $path = $this->photo->store('images/articles', 'public');
        $article = Article::findOrFail($this->articleId);
        $article->images()->create([
            'path' => $path,
        ]);

        $this->reset('photo');
        $this->success('Image uploaded successfully');
    }

    public function delete($id)
    {
        try {
            $image = Image::findOrFail($id);

            // Delete file from storage
            if (Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete DB record
            $image->delete();

            $this->success('Image deleted successfully');
        } catch (\Throwable $th) {
            $this->error('Error deleting image: ' . $th->getMessage());
        }
    }
}; ?>

<div>
    <x-drawer wire:model="showDrawer" class="w-11/12 lg:w-1/3">
        <div class="my-2">
            @if ($this->images->count() > 0)
                @foreach ($this->images as $image)
                    <x-list-item :item="$image" class="w-[80%]">
                        <x-slot:avatar>
                            <img src="{{ asset('storage/' . $image->path) }}" class="w-[25%] h-[25%]"
                                alt="Image {{ $image->path }} could not be loaded" />
                        </x-slot:avatar>
                        <x-slot:actions>
                            <div class="flex items-center justify-end">
                                <x-button label="Delete Image" icon="o-trash" class="bg-red-500 mr-2"
                                    wire:click="delete({{ $image->id }})" spinner />
                            </div>
                        </x-slot:actions>
                    </x-list-item>
                @endforeach
            @else
                <x-alert title="No Images found" description="No images have been found for this article"
                    class="alert-warning" />
            @endif

        </div>
        <div class="flex items-end space-x-2 my-2">
            <x-button label="Close" @click="$wire.showDrawer = false" />
            <x-file wire:model="photo" label="Upload" accept="image/*"/>
            <div wire:loading wire:target="photo" class="text-blue-500" spinner>Uploading...</div>
        </div>
    </x-drawer>

</div>
