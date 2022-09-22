<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        return [
            'name' => $this->faker->name,
            'slug' => $this->faker->slug,
            'description' => $this->faker->text,
            'price' => $this->faker->randomFloat(2, 1, 100),
            'image' => $this->faker->imageUrl(640, 480, 'cats', true),
            'user_id' => $user->id,
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }
}
