<?php
namespace Database\Factories\Package;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Package\PackageItems;

class PackageItemsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PackageItems::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'product_id'	=> 1,
			'package_id'	=> 1,
			'count'			=> 50
		];
    }
}
