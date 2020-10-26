<?php
namespace Database\Factories\HostGroup;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\HostGroup\HostGroup;

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
			'group_id'			=> 1,
			'group_name'		=> $this->faker->name,
			'decription'		=> $this->faker->text,
			'api_group_name'	=> $this->faker->name,
			'user_id'			=> 1,
			'_lft'				=> 1,
			'_rgt'				=> 2,
			'parent_id'			=> 0,
		];
    }
}
