<?php

namespace App\Traits;

use App\Models\Product;

trait HasImage
{
    // public function uploadImage($request, $path)
    // {
    //     $image = null;

    //     if ($request->file('image')) {
    //     $image = $request->file('image');
    //     // $imageName = time() . '.' . $image->getClientOriginalExtension();
    //     $imageName = $image->hashName();

    //     $image->storeAs($path, $imageName);
    //     }

    //     return $image;
    // }

    public function uploadImage($request, $path, $modelInstance)
    {
        $imageName = "";
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();

            $image->storeAs($path, $imageName);

            $modelInstance->image()->create([  // Leverage polymorphic relationship
                'url' => $imageName,
            ]);

        } 
            
        return $imageName; // Optional: Return the saved Image model
    }
}