<?php
namespace Database\Factories\Bank;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Bank\Bank;

class BankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bank::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'amount'		=> 1000,
			'updated_at'	=> now()->addWeek()->timestamp,
			'created_at'	=> now()->timestamp,
		];
    }
}
