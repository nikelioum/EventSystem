<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventsResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'event_date' => $this->event_date ? $this->event_date->format('d-m-Y H:i') : null,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'current_registrations_count' => $this->current_registrations_count,
            'status' => $this->status,
        ];
    }
}
