<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'uuid'         => $this->uuid,
            'phone_number' => $this->phone_number,
            'content'      => $this->content,
            'status'       => $this->status,
            'message_id'   => $this->message_id,
            'sent_at'      => $this->sent_at,
        ];
    }
}
