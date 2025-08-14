<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // $p_id = fake()->numberBetween(1,100);
        // $p_name = fake()->sentence(1,8);
        // $p_price = fake()->numberBetween(000,9);
        // $p_brand = fake()->sentence(2,8);
        // $p_quantity = fake()->numberBetween(1,1000);

        // return [
        //     'product_id' => $p_id,
        //     'product_name'=> $p_name,
        //     'product_brand' => $p_brand,
        //     'quantity' => $p_quantity,
        //     'price' => $p_price,
        // ];
    }
}
