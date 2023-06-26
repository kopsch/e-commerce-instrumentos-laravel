<?php

namespace App\Modules\Account\Permissions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'type'     => $this->type,
            'multiple' => $this->multiple ? true : false,
        ];
    }
}
