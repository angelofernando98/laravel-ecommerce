<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ProductDetailPage extends Component
{

    use LivewireAlert;

    // Get the product slug from the URL and store it in the component's state.
    public $slug;
    public $quantity = 1; // Default quantity showing the product detail page

    public function mount($slug) { 
        $this->slug = $slug;
    }

    public function increaseQty() {
        $this->quantity++;
    }

    public function decreaseQty() {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }


    // ..::ADD PRODUCT TO CART METHOD::..

    // This method adds a product to the cart and then updates the cart count in the navbar
    public function addToCart($product_id) {
        // Get the total count of items in the cart after adding the product
        $total_count = CartManagement::addItemToCart($product_id);

        // Dispatch an event to the navbar component to update the cart count
        // This is done using the Livewire `dispatch` method.
        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        // Show a success message to the user that the product has been added to the cart
        // The message is shown at the bottom end of the screen and disappears after 3 seconds.
        $this->alert('success', 'Product added to cart successfully!', [
            'position' => 'bottom-end',
            'timer' => 3000,
            'toast' => true,
        ]);
    }
    
    // ..::END ADD PRODUCT TO CART METHOD::..



    public function render()
    {
        return view('livewire.product-detail-page', [
            'product' => Product::where('slug', $this->slug)->firstOrFail(),
        ]);
    }
}
