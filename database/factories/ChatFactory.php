<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender' => $this->faker->randomDigit(),
            'recipient' => $this->faker->randomDigit(),
            'message' => $this->faker->sentence(),
            'status' => $this->faker->numberBetween(0, 1),
        ];
    }
}
