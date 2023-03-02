<?php

namespace App\Services;

use App\Repositories\BoardingCardRepository;
use App\Entities\BoardingCard;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

class BoardingCardSorter
{
    /**
     * Boarding card repository (to retirieve all the cards at the beginning of the algorithm).
     */
    private BoardingCardRepository $boardingCardRepository;

    /**
     * Exising boarding cards.
     */
    private Collection $boardingCards;

    /**
     * Sorted boarding cards (final result).
     */
    private $sortedBoardingCards = [];

    /**
     * Boarding cards with the minimal path to each destination (in the form of a graph).
     */
    private $sortedBoardingCardsNodes = [];

    /**
     * Class constructor.
     */
    public function __construct(BoardingCardRepository $boardingCardRepository)
    {
        $this->boardingCardRepository = $boardingCardRepository;
        $this->boardingCards = $this->boardingCardRepository->getAll();
    }

    /**
     * Sort boarding cards.
     * 
     * @param string $src
     * @param string $dest
     * 
     * @return ?array
     */
    public function sort(string $src, string $dest): ?array
    {
        if (!$this->boardingCards) return null;

        $startingCards = $this->boardingCards->where("departureLocation", $src)->all();

        if (count($startingCards) == 0) return null;

        foreach ($startingCards as $startingCard) {
            $this->visitAndSort($dest, null, $startingCard, 0);
        }

        $this->accumulateCards($dest);

        return $this->sortedBoardingCards;
    }

    /**
     * Visit a boarding card that is a child of "parent" and place its node according to its "depth" (how much it gets to reach the destination of the card).
     * 
     * @param string $dest
     * @param ?BoardingCard $parent
     * @param BoardingCard $current
     * @param int $depth
     * 
     * (Note: this function modifies the $sortedBoardingCardsNodes property)
     * @return void
     */
    private function visitAndSort(string $dest, ?BoardingCard $parent, BoardingCard $current, int $depth): void
    {
        $existingDestIndex = $this->getExistingDestIndex($current["arrivalLocation"]);
        $existingDest = $this->sortedBoardingCardsNodes[$existingDestIndex] ?? null;

        $node = [
            "from" => $parent["id"] ?? null,
            "to" => $current["id"],
            "depth" => $depth,
        ];

        if (!$existingDest) {
            $this->sortedBoardingCardsNodes[] = $node;
        } else if ($existingDest["depth"] > $depth) {
            $this->sortedBoardingCardsNodes[$existingDestIndex] = $node;
        } else return;

        if ($current["arrivalLocation"] === $dest) return;

        foreach ($this->getChildren($current) as $child) $this->visitAndSort($dest, $current, $child, $depth + 1);
    }

    /**
     * Get all children of a "parent" card according to its arrivalLocation
     * (all the cards that has a "departureLocation" similar to the "arrivalLocation" of "parent").
     * 
     * @param BoardingCard $parent
     * 
     * @return array
     */
    private function getChildren(BoardingCard $parent): array
    {
        return $this->boardingCards->where("departureLocation", $parent["arrivalLocation"])->all();
    }

    /**
     * Get the index of the node that has the same destination as the given one.
     * 
     * @param string $dest
     * 
     * @return ?int
     */
    private function getExistingDestIndex(string $dest): ?int
    {
        foreach ($this->sortedBoardingCardsNodes as $index => $boardingCardNode) {
            if ($this->boardingCards->find($boardingCardNode["to"])["arrivalLocation"] === $dest) {
                return $index;
            }
        }

        return null;
    }

    /**
     * Get the node that has the same destination as the given one.
     * 
     * @param string $dest
     * 
     * @return ?array
     */
    private function getNodeByDest(string $dest): ?array
    {
        foreach ($this->sortedBoardingCardsNodes as $boardingCardNode) {
            if ($this->boardingCards->find($boardingCardNode["to"])["arrivalLocation"] === $dest) {
                return $boardingCardNode;
            }
        }

        return null;
    }

    /**
     * Get the node that has the same "to" card as the given one (by ID).
     * 
     * @param int $id
     * 
     * @return ?array
     */
    private function getNodeByDestID(int $id): ?array
    {
        foreach ($this->sortedBoardingCardsNodes as $boardingCardNode) {
            if ($boardingCardNode["to"] === $id) {
                return $boardingCardNode;
            }
        }

        return null;
    }

    /**
     * Accumulate the sorted boarding card nodes to achieve the most optimised path,
     * starting from "dest" until reaching the "src" (the node that has no parent).
     * 
     * @param string $dest
     * 
     * (Note: this function modifies the $sortedBoardingCards property)
     * @return void
     */
    private function accumulateCards(string $dest): void
    {
        $currentNode = $this->getNodeByDest($dest);
        if (!$currentNode) {
            $this->sortedBoardingCards = null;
            return;
        }

        while ($currentNode["from"] != null) {
            $this->sortedBoardingCards = Arr::prepend($this->sortedBoardingCards, $this->boardingCards->find($currentNode["to"]));
            $currentNode = $this->getNodeByDestID($currentNode["from"]);

            info([
                "cuurent node" => $currentNode
            ]);
        }
        $this->sortedBoardingCards = Arr::prepend($this->sortedBoardingCards, $this->boardingCards->find($currentNode["to"]));
    }
}
