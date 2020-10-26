<?php
namespace Database\Factories\Host;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Host\Host;

class HostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Host::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'host'			=> $this->faker->name,
			'api_host_name'	=> $this->faker->name
		];
    }
}
