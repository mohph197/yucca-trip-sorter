<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

use App\Entities\BoardingCard;

class BoardingCardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the API returns all the cards in the database.
     */
    public function test_get_all_cards(): void
    {
        $numberOfCards = 10;

        BoardingCard::factory()->count($numberOfCards)->create();

        $response = $this->get('/api/boarding-cards');

        $response
            ->assertStatus(200)
            ->assertJsonCount($numberOfCards, 'data')
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data',
                    $numberOfCards,
                    fn ($json) =>
                    $json
                        ->hasAll([
                            'id',
                            'departureLocation',
                            'arrivalLocation',
                            'transportType',
                        ])
                        ->etc()
                )
            );
    }

    /**
     * Test that the API returns a single card by its ID.
     */
    public function test_get_card_by_id(): void
    {
        $insertedCard = BoardingCard::factory()->create();

        $response = $this->get('/api/boarding-cards/' . $insertedCard->id);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data',
                    fn ($json) =>
                    $json
                        ->hasAll([
                            'id',
                            'departureLocation',
                            'arrivalLocation',
                            'transportType',
                        ])
                        ->etc()
                )
            );
    }

    /**
     * Test the return message when a card is not found.
     */
    public function test_card_by_id_not_found(): void
    {
        $response = $this->get('/api/boarding-cards/1');

        $response
            ->assertStatus(404)
            ->assertExactJson([
                'message' => 'Boarding card not found',
            ]);
    }

    /**
     * Test tthe creation of a new card.
     */
    public function test_create_card(): void
    {
        $card = BoardingCard::factory()->make();

        $response = $this->post('/api/boarding-cards', [
            'departureLocation' => $card->departureLocation,
            'arrivalLocation' => $card->arrivalLocation,
            'transportType' => $card->transportType,
            'seatNumber' => $card->seat ?? null,
            'gateNumber' => $card->gate ?? null,
            'baggageDrop' => $card->baggageDrop ?? null,
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->has(
                    'data',
                    fn ($json) =>
                    $json
                        ->has('id')
                        ->where('departureLocation', $card->departureLocation)
                        ->where('arrivalLocation', $card->arrivalLocation)
                        ->where('transportType', $card->transportType)
                        ->etc()
                )
            );
    }

    /**
     * Test deletion of a card.
     */
    public function test_delete_card(): void
    {
        $card = BoardingCard::factory()->create();

        $response = $this->delete('/api/boarding-cards/' . $card->id);

        $response
            ->assertStatus(204)
            ->assertNoContent();
    }

    /**
     * Test sorting cards (possible case)
     */
    public function test_sort_cards(): void
    {
        BoardingCard::create([
            'departureLocation' => 'A',
            'arrivalLocation' => 'B',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'B',
            'arrivalLocation' => 'C',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'A',
            'arrivalLocation' => 'E',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'D',
            'arrivalLocation' => 'F',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'C',
            'arrivalLocation' => 'D',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'E',
            'arrivalLocation' => 'G',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'B',
            'arrivalLocation' => 'D',
            'transportType' => 'test',
        ]);

        $response = $this->get('/api/boarding-cards/sort?src=A&dest=F');

        $response
            ->assertStatus(200)
            ->assertExactJson([
                "data" => [
                    "1. From A, take test to B, No seat assignment.",
                    "2. From B, take test to D, No seat assignment.",
                    "3. From D, take test to F, No seat assignment.",
                    "4. You have arrived at your final destination.",
                ],
            ]);
    }

    /**
     * Test sorting cards (impossible case)
     */
    public function test_sort_cards_impossible(): void
    {
        BoardingCard::create([
            'departureLocation' => 'A',
            'arrivalLocation' => 'B',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'B',
            'arrivalLocation' => 'C',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'A',
            'arrivalLocation' => 'E',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'D',
            'arrivalLocation' => 'F',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'C',
            'arrivalLocation' => 'D',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'E',
            'arrivalLocation' => 'G',
            'transportType' => 'test',
        ]);
        BoardingCard::create([
            'departureLocation' => 'B',
            'arrivalLocation' => 'D',
            'transportType' => 'test',
        ]);

        $response = $this->get('/api/boarding-cards/sort?src=D&dest=G');

        $response
            ->assertStatus(400)
            ->assertExactJson([
                'message' => 'Unable to sort boarding cards to reach your destination',
            ]);
    }
}
