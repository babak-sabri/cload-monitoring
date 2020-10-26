<?php
namespace Database\Factories\Host;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Host\HostInterface;

class HostInterfaceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HostInterface::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'hostid'	=> 1,
			'type'		=> 1,
			'main'		=> 1,
			'useip'		=> 1,
			'ip'		=> $this->faker->ipv4,
			'dns'		=> '',
			'port'		=> 1,
		];
    }
}
