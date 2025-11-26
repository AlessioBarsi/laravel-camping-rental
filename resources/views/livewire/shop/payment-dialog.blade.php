<?php

use Livewire\Volt\Component;
use Mary\Traits\Toast;
use App\Models\ArticleInStore;

new class extends Component {
    use Toast;
    public bool $openModal = false;
    public $method;
    public string $startDate;
    public string $endDate;
    public float $total = 0.0;

    public function mount($total)
    {
        $this->total = $total;
    }

    #[\Livewire\Attributes\On('show-payment-dialog')]
    public function showModal()
    {
        $this->openModal = true;
    }

    public function submit() {
        $this->success($this->startDate . " method: ". $this->method);
/*         try {
            Rental::create([
                'article_in_store_id' => 1,
                'user_id' => 1,
                'rented_at' => $this->startDate,
                'returned_at' => $this->endDate,
                'notes',
            ]);
            $this->success('Order created');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        } */
    }
}; ?>

<div>
    <x-form>
        <x-modal :open="$openModal" x-on:close="$wire.set('openModal', false)" wire:model="openModal"
            title="Confirm your Order" class="backdrop-blur">
            Total to pay: {{ $this->total }}$

            <x-form wire:submit="save">
                <x-datetime label="Pick up Date" wire:model="startDate" required/>
                <x-datetime label="Return Date" wire:model="endDate" required/>

                <x-radio label="Select your payment method" wire:model="method" :options="[
                    ['id' => 1, 'name' => 'Cash', 'hint' => 'Pay on pickup'],
                    ['id' => 2, 'name' => 'Card', 'hint' => 'Powered by Stripe'],
                ]" inline />
            </x-form>

            <x-slot:actions>
                <x-button label="Confirm" class="btn-primary" type="submit" spinner />
                <x-button label="Cancel" wire:click="$set('openModal', false)" />
            </x-slot:actions>
        </x-modal>

    </x-form>
</div>
