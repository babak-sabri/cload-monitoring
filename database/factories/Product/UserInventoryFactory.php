<?php
namespace Database\Factories\Product;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product\UserInventory;

class UserInventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserInventory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'user_id'		=> 1,
			'product_id'	=> 1,
			'product_count'	=> 1,
			'updated_at'	=> now()->addWeek()->timestamp,
			'created_at'	=> now()->timestamp,
		];
    }
}
