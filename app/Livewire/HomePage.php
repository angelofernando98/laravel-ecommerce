<?php

namespace App\Livewire;

use App\Models\Brand;
use App\Models\Category;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Home Page - Angel Shop')]

class HomePage extends Component
{
    public function render()
    {
        $brands = Brand::where('is_active', 1)->get(); // get all active brands
        $categories = Category::where('is_active', 1)->get(); // get all active categories
        return view('livewire.home-page', [
            'brands' => $brands,
            'categories' => $categories,

        ]);
    }
}
