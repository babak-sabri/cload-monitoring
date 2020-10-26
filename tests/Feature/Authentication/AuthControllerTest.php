<?php
namespace Tests\Feature\Authentication;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Users\User;

class AuthControllerTest extends TestCase
{
	use RefreshDatabase;

	/**
     * login user by cellphone
     *
     * @return void
     */
    public function testLoginByCellphone()
    {
		\Artisan::call('passport:install');
		User::factory()->create([
			'cellphone' => '09192332841',
			'password'	=> Hash::make('testpassword')
		]);
		$this->post(route('auth.login'), [
			'login_type'	=> 'cellphone',
			'cellphone'		=> '09192332841',
			'password'		=> 'testpassword',
			'remember_me'	=> 0
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		->assertJson(['data' => ['token_type' => 'Bearer']])
		->assertJsonStructure([
			'data'	=> [
				'access_token',
				'token_type',
				'expires_at'
			]
		])
		;
    }
	
	/**
     * login user by cellphone
     *
     * @return void
     */
    public function testFailLoginByCellphone()
    {
		\Artisan::call('passport:install');
		User::factory()->create([
			'cellphone' => '09192332841',
			'password'	=> Hash::make('testpassword')
		]);

		$this->post(route('auth.login'), [
			'login_type'	=> 'cellphone',
			'cellphone'		=> '09192332841p',
			'password'		=> 'testpassword',
			'remember_me'	=> 0
		])
		->assertStatus(Response::HTTP_UNAUTHORIZED)
		;
    }
	
	/**
     * login user by username
     *
     * @return void
     */
    public function testLogout()
    {
		
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$this->post(route('auth.logout'), [],
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		;
    }
	
	public function testNotVerifiedLoginByCellphone()
    {
		\Artisan::call('passport:install');
		User::factory()->create([
			'cellphone'				 => '09192332841',
			'password'				=> Hash::make('testpassword'),
			'cellphone_verified_at'	=> null,
		]);
		$this->post(route('auth.login'), [
			'login_type'	=> 'cellphone',
			'cellphone'		=> '09192332841',
			'password'		=> 'testpassword',
			'remember_me'	=> 0
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNAVAILABLE_FOR_LEGAL_REASONS)
		;
    }
}
