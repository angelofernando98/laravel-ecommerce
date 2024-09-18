<?php

namespace App\Livewire;

use App\Helpers\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Products - Angel Shop')]

class ProductsPage extends Component {

    use LivewireAlert;
    
    use WithPagination;

    #[Url()]
    public $selected_categories = [];

    #[Url()]
    public $selected_brands = [];

    #[Url()]
    public $featured;

    #[Url()]
    public $on_sale;

    #[Url()]
    public $price_range = 0;

    public $sort = 'latest';



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



    public function render() {
        $productQuery = Product::query()->where('is_active', 1);

        // If categories are selected, filter products by those categories
        // This allows us to show only products that are part of the selected categories
        if (!empty($this->selected_categories)) {
            // Using whereIn to filter products by category_ids
            $productQuery->whereIn('category_id', $this->selected_categories);
        }


        if (!empty($this->selected_brands)) {
            // Using whereIn to filter products by category_ids
            $productQuery->whereIn('brand_id', $this->selected_brands);
        }


        if($this->featured) {
            $productQuery->where('is_featured', 1);
        }


        if($this->on_sale) {
            $productQuery->where('on_sale', 1);
        }


        if($this->price_range) {
            $productQuery->whereBetween('price', [0, $this->price_range]);
        }

        if($this->sort == 'latest') {
            $productQuery->latest();
        }

        if($this->sort == 'price') {
            $productQuery->orderBy('price');
        }


        // We query the products that are active and if any categories are selected,
        // we filter the products by those categories.
        // The products are paginated with 6 products per page.
        // We also get all active brands and categories to display in the sidebar.
        return view('livewire.products-page', [
            // The products to be displayed
            'products' => $productQuery->paginate(6),
            // All active brands
            'brands' => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
            // All active categories
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),        
        ]);
    }
    
}
