<?php

namespace App\Helpers;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartManagement
{


    // ..::ADD ITEMS TO THE CART::..

    /**
     * Check if the product_id is already in the cart 
     * if yes, increment the quantity and update the total
     * if no, add the product to the cart 
     */
    static public function addItemToCart($product_id) {
        // get all cart items from cookie
        $cart_items = self::getCartItemsFromCookie();

        // check if the product is already in the cart
        $existing_item = null;
        foreach($cart_items as $key => $item) {
            if($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        // if the product is already in the cart
        if($existing_item !== null) {
            // increment the quantity and update the total
            $cart_items[$existing_item]['quantity']++;
            $cart_items[$existing_item]['total'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        } else {
            // add the product to the cart 
            $product = Product::where('id', $product_id)->first(['id', 'name', 'price','images']);
            if($product){
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->images,
                    'quantity' => 1,
                    'unit_amount' => $product->price,
                    'total_amout' => $product->price
                ];
            }
        }

        // add the cart items to the cookie
        self::addCartItemsToCookie($cart_items);
        // return the count of cart items
        return count($cart_items);
    }


    // ..::ADD ITEMS TO THE CART WITH QUANTITY::..

    static public function addItemToCartWithQty($product_id, $qty = 1) {
        // get all cart items from cookie
        $cart_items = self::getCartItemsFromCookie();

        // check if the product is already in the cart
        $existing_item = null;
        foreach($cart_items as $key => $item) {
            if($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        // if the product is already in the cart
        if($existing_item !== null) {
            // increment the quantity and update the total
            $cart_items[$existing_item]['quantity'] = $qty;
            $cart_items[$existing_item]['total'] = $cart_items[$existing_item]['quantity'] * $cart_items[$existing_item]['unit_amount'];
        } else {
            // add the product to the cart 
            $product = Product::where('id', $product_id)->first(['id', 'name', 'price','images']);
            if($product){
                $cart_items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->images,
                    'quantity' => $qty,
                    'unit_amount' => $product->price,
                    'total_amout' => $product->price
                ];
            }
        }

        // add the cart items to the cookie
        self::addCartItemsToCookie($cart_items);
        // return the count of cart items
        return count($cart_items);
    }
    

    
    
    // ..::REMOVE ITEM FROM THE CART::..

     /** 
     * This method removes an item from the cart.
     * It loops through the cart items and if the product id matches the one passed in the parameter
     * it unsets the item from the array.
     * Then it adds the updated cart items to the cookie.
     */
    static public function removeCartItem($product_id) {
        $cart_items = self::getCartItemsFromCookie();
        foreach($cart_items as $key => $item) {
            // if the product id matches the one passed in the parameter
            if($item['product_id'] == $product_id) {
                // unset the item from the array
                unset($cart_items[$key]);
            }
        }
        // add the updated cart items to the cookie
        self::addCartItemsToCookie($cart_items);

        // return the updated cart items
        return $cart_items;
    }





    // ..::ADD ITEMS TO COOKIE::..
    static public function addCartItemsToCookie($cart_items) {
        Cookie::queue('cart_items', json_encode($cart_items), 60 * 24 * 30);
    }


    // ..::CLEAR CART ITEMS FROM COOKIE::..
    static public function clearCartItems() {
        Cookie::queue(Cookie::forget('cart_items'));
    }
    

    // ..::GET ALL CART ITEMS FROM COOKIE::..
    static public function getCartItemsFromCookie() {
        $cart_items = json_decode(Cookie::get('cart_items'), true);
        if(!$cart_items) {
            $cart_items = [];
        }
        return $cart_items; // Return the array
    }


    // ..::INCREMENT ITEM QUANTITY::..
    public function incrementQuantityToCartItem($product_id) {
        // Get the cart items from the cookie
        $cart_items = self::getCartItemsFromCookie();

        // Loop through the cart items and if the product_id matches the one passed in
        foreach($cart_items as $key => $item) {
            if($item['product_id'] == $product_id) {
                // Increment the quantity of the item
                $cart_items[$key]['quantity']++;

                // Update the total amount of the item
                $cart_items[$key]['total_amout'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
            }
        }

        // Add the updated cart items to the cookie
        self::addCartItemsToCookie($cart_items);

        // Return the updated cart items
        return $cart_items;
    }



    // ..::DECREMENT ITEM QUANTITY::..
    public function decrementQuantityToCartItem($product_id) {
        $cart_items = self::getCartItemsFromCookie();
        foreach($cart_items as $key => $item) {
            if($item['product_id'] == $product_id) {
                if($cart_items[$key]['quantity'] > 1) {
                    $cart_items[$key]['quantity']--;
                    $cart_items[$key]['total_amout'] = $cart_items[$key]['quantity'] * $cart_items[$key]['unit_amount'];
                }
            }
        }
        self::addCartItemsToCookie($cart_items);
        return $cart_items;
    }



    // calculate grand total
    static public function calculateGrandTotal($items) {
        return array_sum(array_column($items, 'total_amout'));
        
    }

}


        /*
         * Here's how the steps should be ordered for a customer who wants to add an item to the cart and purchase it:
         *
         * 1. getCartItemsFromCookie() - First, retrieve any existing cart items to check if the product is already in the cart.
         * 2. addItemToCart($product_id) - Add the selected product to the cart or update the quantity if the item is already in the cart.
         * 3. incrementQuantityToCartItem($product_id) - (Optional) If the customer adds more of the same item, increment the quantity.
         * 4. decrementQuantityToCartItem($product_id) - (Optional) If the customer reduces the quantity, decrement the number of items.
         * 5. addCartItemsToCookie($cart_items) - Save the updated cart items in the cookie after adding or modifying the items.
         * 6. calculateGrandTotal($items) - Calculate the total price for all items in the cart.
         * 7. removeCartItem($product_id) - (Optional) If the customer decides to remove an item from the cart before purchasing.
         * 8. clearCartItems() - Once the purchase is completed, clear the cart items.
         */



?>