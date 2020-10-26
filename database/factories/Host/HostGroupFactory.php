<?php
namespace Database\Factories\Host;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Host\HostGroup;

class HostGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HostGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'hostid'	=> 1,
			'group_id'	=> 2
		];
    }
}
