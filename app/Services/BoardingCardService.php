<?php

namespace App\Services;

use App\Entities\BoardingCard;
use Illuminate\Http\Request;
use App\Http\Resources\BoardingCardResource;
use App\Http\Resources\SortedCardsResource;
use App\Repositories\BoardingCardRepository;
use App\Services\BoardingCardSorter;
use Illuminate\Support\Facades\Validator;

class BoardingCardService
{
    protected $boardingCardSorter;
    protected $boardingCardRepository;

    /**
     * Class constructor.
     */
    public function __construct(
        BoardingCardSorter $boardingCardSorter,
        BoardingCardRepository $boardingCardRepository
    ) {
        $this->boardingCardSorter = $boardingCardSorter;
        $this->boardingCardRepository = $boardingCardRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $boardingCards = $this->boardingCardRepository->getAll();
        if (!$boardingCards) {
            return response()->json([
                'message' => 'Failed to get boarding cards',
            ], 400);
        }

        return BoardingCardResource::collection($boardingCards);
    }

    /**
     * Return the specified resource.
     */
    public function get(BoardingCard $boardingCard)
    {
        if (!$boardingCard) {
            return response()->json([
                'message' => 'Failed to get boarding card',
            ], 400);
        }

        return new BoardingCardResource($boardingCard);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /**
         * Validate required attributes.
         */
        $validator = Validator::make($request->all(), [
            'departureLocation' => 'required',
            'arrivalLocation' => 'required',
            'transportType' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Failed to create boarding card',
                'errors' => $validator->errors(),
            ], 400);
        }

        $boardingCard = $this->boardingCardRepository->save($request->all());

        if (!$boardingCard) {
            return response()->json([
                'message' => 'Failed to create boarding card',
            ], 400);
        }

        return new BoardingCardResource($boardingCard);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BoardingCard $boardingCard)
    {
        if (!$this->boardingCardRepository->update($boardingCard, $request->all())) {
            return response()->json([
                'message' => 'Failed to update boarding card',
            ], 400);
        }

        return new BoardingCardResource($boardingCard);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BoardingCard $boardingCard)
    {
        if (!$this->boardingCardRepository->delete($boardingCard)) {
            return response()->json([
                'message' => 'Failed to delete boarding card',
            ], 400);
        }

        return response(null, 204);
    }

    /**
     * Sort the boarding cards.
     */
    public function sort(Request $request)
    {
        $sortingResults = $this->boardingCardSorter->sort($request->query('src'), $request->query('dest'));
        if (!$sortingResults) {
            return response()->json([
                'message' => 'Unable to sort boarding cards to reach your destination',
            ], 400);
        }

        return new SortedCardsResource($sortingResults);
    }

    /**
     * Handle ModelNotFoundException.
     */
    public static function missing()
    {
        return response()->json([
            'message' => 'Boarding card not found',
        ], 404);
    }
}
