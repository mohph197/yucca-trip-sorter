<?php

namespace Database\Factories;

use App\Entities\BoardingCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Entities\BoardingCard>
 */
class BoardingCardFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BoardingCard::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'departureLocation' => $this->faker->city(),
            'arrivalLocation' => $this->faker->city(),
            'transportType' => $this->faker->randomElement(['train', 'bus', 'plane']),
            'seatNumber' => $this->faker->randomElement([null, strtoupper($this->faker->randomLetter()) . $this->faker->randomNumber(3)]),
            'gateNumber' => $this->faker->randomElement([null, strtoupper($this->faker->randomLetter()) .  $this->faker->randomNumber(2)]),
            'baggageDrop' => $this->faker->randomElement([null, $this->faker->sentence()]),
        ];
    }
}
