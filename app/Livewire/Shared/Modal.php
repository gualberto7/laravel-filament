<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use Livewire\Attributes\On;

class Modal extends Component
{
    #[On('addCheckin')] 
    public function addCheckin($data)
    {
        $this->dispatch('open-modal', id: 'search-client');
    }
    public function render()
    {
        return view('livewire.shared.modal');
    }
}
