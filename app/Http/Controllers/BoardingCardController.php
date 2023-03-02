<?php

namespace App\Http\Controllers;

use App\Entities\BoardingCard;
use Illuminate\Http\Request;
use App\Services\BoardingCardService;

class BoardingCardController extends Controller
{
    protected $boardingCardService;

    public function __construct(BoardingCardService $boardingCardService)
    {
        $this->boardingCardService = $boardingCardService;
    }

    public function index()
    {
        return $this->boardingCardService->index();
    }

    public function get(BoardingCard $boardingCard)
    {
        return $this->boardingCardService->get($boardingCard);
    }

    public function store(Request $request)
    {
        return $this->boardingCardService->store($request);
    }

    public function update(Request $request, BoardingCard $boardingCard)
    {
        return $this->boardingCardService->update($request, $boardingCard);
    }

    public function destroy(BoardingCard $boardingCard)
    {
        return $this->boardingCardService->destroy($boardingCard);
    }

    public function sort(Request $request)
    {
        return $this->boardingCardService->sort($request);
    }
}
