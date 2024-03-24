<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class newProductMaxImages implements Rule
{
    // private $productId;

    // public function __construct($productId)
    // {
    //     $this->productId = $productId;
    // }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function passes($attribute, $value)
    {
        // $existingImages = \App\Models\Image::where('imageable_id', $this->productId)
        //     ->where('imageable_type', \App\Models\Product::class)
        //     ->count();
        
        return count($value) <= 5;
    }

    public function message()
    {
        return "You can't upload more than 5 images for this product.";
    }
}
