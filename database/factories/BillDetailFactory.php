<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BillDetail>
 */
class BillDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantity' => fake()->biasedNumberBetween(0, 5, 'sqrt'),

            'product_id' => Product::factory(),
            // Values of the sale: the product values at the time of the sale.
            'price' => fn (array $attributes) => Product::find($attributes['product_id'])?->price,
            'productName' => fn (array $attributes) => Product::find($attributes['product_id'])?->name,
            'bill_id' => Bill::factory(),

            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }
}