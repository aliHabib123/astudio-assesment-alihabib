<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status_id' => $this->status_id,
            'status' => new ProjectStatusResource($this->whenLoaded('status')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'attribute_values' => AttributeValueResource::collection($this->whenLoaded('attributeValues')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
