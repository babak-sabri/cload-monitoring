<?php
namespace Database\Factories\Product;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product\Product;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'title'			=> $this->faker->name,
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> ITEM,
			'product_cat'	=> COUNTABLE,
			'updated_at'	=> now()->addWeek()->timestamp,
			'created_at'	=> now()->timestamp,
		];
    }
}
