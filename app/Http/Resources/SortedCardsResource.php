<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SortedCardsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        $result = [];
        foreach ($this->resource as $index => $card) {
            $output = $index + 1 . '. ';
            $output .= 'From ' . $card->departureLocation . ', ';
            $output .= 'take ' . $card->transportType;
            $output .= ' to ' . $card->arrivalLocation;

            $output .= ($card->seatNumber) ? (', Sit in seat ' . $card->seatNumber) : ', No seat assignment';

            $output .= ($card->gateNumber) ? (', Gate ' . $card->gateNumber) : '';

            $output .= ($card->baggageDrop) ? (', Baggage drop: ' . $card->baggageDrop) : '';

            $result[] = $output . '.';
        }

        $result[] = count($result) + 1 . '. You have arrived at your final destination.';

        return $result;
    }
}
