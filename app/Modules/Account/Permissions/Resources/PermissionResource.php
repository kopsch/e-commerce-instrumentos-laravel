<?php

namespace App\Modules\Account\Permissions\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
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
            'id'                  => $this->id,
            'name'                => $this->name,
            'type'                => $this->type,
            'allow'               => $this->pivot->allow ? true : false,
            'permission_category' => new PermissionCategoryResource($this->permission_category),
        ];
    }
}
