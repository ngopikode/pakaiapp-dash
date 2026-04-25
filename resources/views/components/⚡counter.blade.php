<?php

use Livewire\Component;

new class extends Component {
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }
};
?>

<div>
    <h1>{{ $count }}</h1>

    <button wire:click="increment" class="btn btn-primary">+</button>
    <button wire:click="decrement" class="btn btn-danger">-</button>

    <br/>

</div>
