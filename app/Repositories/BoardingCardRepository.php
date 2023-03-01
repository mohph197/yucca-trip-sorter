<?php

namespace App\Repositories;

use App\Entities\BoardingCard;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;

class BoardingCardRepository
{
    /**
     * Get all boarding cards.
     * 
     * @return ?Collection
     */
    public function getAll(): ?Collection
    {
        try {
            return BoardingCard::all();
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Create a new boarding card.
     * 
     * @param array $data
     * 
     * @return ?BoardingCard
     */
    public function save(array $data): ?BoardingCard
    {
        try {
            return BoardingCard::create(Arr::only($data, (new BoardingCard())->getFillable()));
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * Update a boarding card.
     * 
     * @param BoardingCard $boardingCard
     * @param array $data
     * 
     * @return bool
     */
    public function update(BoardingCard $boardingCard, array $data): bool
    {
        try {
            return $boardingCard->update(Arr::only($data, (new BoardingCard())->getFillable()));
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Delete a boarding card.
     * 
     * @param string $id
     * 
     * @return bool
     */
    public function delete(BoardingCard $boardingCard): bool
    {
        try {
            return $boardingCard->delete() ?? false;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
