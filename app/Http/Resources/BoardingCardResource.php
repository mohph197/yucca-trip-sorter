<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardingCardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataArray = [
            'id' => $this->id,
            'departureLocation' => $this->departureLocation,
            'arrivalLocation' => $this->arrivalLocation,
            'transportType' => $this->transportType,
        ];

        ($this->seatNumber) && ($dataArray['seatNumber'] = $this->seatNumber);

        ($this->gateNumber) && ($dataArray['gateNumber'] = $this->gateNumber);

        ($this->baggageDrop) && ($dataArray['baggageDrop'] = $this->baggageDrop);

        return $dataArray;
    }
}
