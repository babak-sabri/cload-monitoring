<?php
namespace Database\Factories\Invoice;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice\Invoice;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

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
