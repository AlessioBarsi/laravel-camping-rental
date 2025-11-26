<?php

use Livewire\Volt\Component;
use App\Models\Rental;

new class extends Component {
    
    public function getRentalsProperties() {
        return Rental::where('user_id' => 1)->get();
    }
}; ?>

<div>
    <ul>

    @foreach ($this->rentals as $rental)
        <li>
            User: {{ $rental->user_id }}
            Article: {{ $rental->article_in_store_id }}
        </li>
    @endforeach
    </ul>
</div>
