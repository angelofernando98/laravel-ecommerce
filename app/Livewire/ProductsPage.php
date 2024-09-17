<?php

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Products - Angel Shop')]

class ProductsPage extends Component
{
    public function render()
    {
        return view('livewire.products-page');
    }
}
