<?php
namespace Database\Factories\Bank;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Bank\PaymentLog;

class PaymentLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'user_id'		=> 1,
			'price'			=> 100,
			'entity_id'		=> $this->faker->randomNumber(),
			'pay_for'		=> $this->faker->randomElement(config('payment.pay-for')),
			'updated_at'	=> now()->addWeek()->timestamp,
			'created_at'	=> now()->timestamp,
		];
    }
}
