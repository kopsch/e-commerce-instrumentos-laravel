<?php

namespace App\Modules\Account\Users\Resources;

use App\Modules\Account\Profiles\Resources\ProfileResource;
use App\Modules\Misc\Files\Resources\FileResource;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id'      => $this->id,
            'name'    => $this->name,
            'email'   => $this->email,
            'profile' => new ProfileResource($this->profile)
        ];
    }
}
