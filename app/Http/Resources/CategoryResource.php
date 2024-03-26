<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public $isDetail;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->image->url
        ];

        if ($this->isDetail) {
            $data['created_at'] = $this->created_at;
            $data['updated_at'] = $this->updated_at;
            $data['deleted_at'] = $this->deleted_at;
            $data['products'] = $this->products;
        }

        return $data;
    }

    public function withDetail()
    {
        $this->isDetail = true;

        return $this;
    }
}
