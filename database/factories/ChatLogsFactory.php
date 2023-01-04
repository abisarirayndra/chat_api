<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatLogsFactory extends Factory
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
            'unread_count' => $this->faker->numberBetween(1, 10),
            'latest_message' => $this->faker->sentence(),
        ];
    }
}
