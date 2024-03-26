<?php

namespace App\Traits;

use App\Models\Product;

trait HasImage
{
    public function uploadImage($request, $path, $modelInstance)
    {
        $imageName = "";
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();

            $image->storeAs($path, $imageName);

            $modelInstance->image()->create([
                'url' => $imageName,
            ]);

        } 
            
        return $imageName;
    }
}