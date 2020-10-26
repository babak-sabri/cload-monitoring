<?php

namespace Database\Factories\Users;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Users\Verify;

class VerifyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Verify::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'user_id'			=> 1,
			'verification_code'	=> '123456',
			'expiration_date'	=> now()->timestamp
        ];
    }
}
