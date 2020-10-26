<?php
namespace Database\Factories\Currency;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Currency\Currency;

class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'currency_title'	=> $this->faker->text,
			'currency_price'	=> 1000,
			'updated_at'		=> now()->addWeek()->timestamp,
			'created_at'		=> now()->timestamp,
        ];
    }
}
