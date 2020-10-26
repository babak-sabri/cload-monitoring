<?php

namespace Database\Factories\Users;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Users\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
			'first_name'			=> $this->faker->name,
			'last_name'				=> $this->faker->lastName,
			'email'					=> $this->faker->unique()->safeEmail,
			'cellphone'				=> $this->faker->unique()->safeEmail,
			'email_verified_at'		=> now(),
			'cellphone_verified_at'	=> now()->timestamp,
			'profile_image'			=> 'testfile',
			'password'				=> '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
			'remember_token'		=> Str::random(10),
			'job_title'				=> Str::random(10),
			'gender'				=> 'male',
			'language'				=> 'fa',
			'calendar_type'			=> 'jalali',
			'user_type'				=> 'admin',
			'timezone'				=> 'Asia/Tehran',
			'how_to_find'			=> Str::random(100),
			'expiration_date'		=> now()->addWeek()->timestamp,
        ];
    }
}
