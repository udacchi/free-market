<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * 定義するモデルのデフォルトデータを設定
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'price' => $this->faker->numberBetween(10, 100000),
            'description' => $this->faker->sentence(),
            'image_path' => $this->faker->imageUrl(),
            'condition' => $this->faker->randomElement(['良好', '目立った傷や汚れなし', 'やや傷や汚れあり', '状態が悪い']),
            'user_id' => User::factory(),
            'buyer_id' => null,
        ];
    }
}
