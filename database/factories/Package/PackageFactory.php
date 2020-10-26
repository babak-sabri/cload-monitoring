<?php
namespace Database\Factories\Package;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Package\Package;

class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'title'			=> $this->faker->name(),
			'description'	=> $this->faker->text(),
			'price'			=> 500,
			'status'		=> 1,
			'updated_at'	=> now()->addWeek()->timestamp,
			'created_at'	=> now()->timestamp,
		];
    }
}
