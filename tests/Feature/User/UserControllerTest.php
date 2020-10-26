<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\Users\User;
use App\Models\Users\Verify;
use App\Helpers\Str;

class UserControllerTest extends TestCase
{
	use RefreshDatabase;
	
	/**
	 * store new user required data
	 *
	 * @return void
	 */
	public function testStoreUser()
	{
		$this->post(route('user.store'), [
			'cellphone'				=> '09192332269',
			'email'					=> 'test@sdfsdf.sdf',
			'password'				=> '123456',
			'password_confirmation'	=> '123456',
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->assertJson([
			'data' => [
				'id' => 1
			]
		])
		;
		
		$userModel		= new User();
		$verifyModel	= new Verify();
		$this->assertDatabaseHas($userModel->getTable(), [
			'id'		=> 1,
			'cellphone'	=> '09192332269',
			'email'		=> 'test@sdfsdf.sdf',
		]);
		$this->assertDatabaseHas($verifyModel->getTable(), [
			'user_id'	=> 1
		]);
	}
	
	/**
	 * store new user with all data
	 *
	 * @return void
	 */
	public function testStoreUserWithAllData()
	{
		$this->post(route('user.store'), [
			'first_name'			=> 'first name',
			'last_name'				=> 'last name',
			'gender'				=> 'male',
			'organization'			=> 'organization name',
			'cellphone'				=> '09192332861',
			'email'					=> 'test@sdfsdf.sdf',
			'password'				=> '123456',
			'password_confirmation'	=> '123456',
			'job_title'				=> 'test job title',
			'how_to_find'			=> 'test how to find',
			'timezone'				=> 'Asia/Tehran'
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->assertJson([
			'data' => [
				'id' => 1
			]
		])
		;
		
		$userModel		= new User();
		$verifyModel	= new Verify();
		$this->assertDatabaseHas($userModel->getTable(), [
			'first_name'			=> 'first name',
			'last_name'				=> 'last name',
			'gender'				=> 'male',
			'organization'			=> 'organization name',
			'cellphone'				=> '09192332861',
			'email'					=> 'test@sdfsdf.sdf',
			'job_title'				=> 'test job title',
			'how_to_find'			=> 'test how to find',
			'timezone'				=> 'Asia/Tehran'
		]);
		$this->assertDatabaseHas($verifyModel->getTable(), [
			'user_id'	=> 1
		]);
	}
	
	/**
	 * test duplicate email
	 * 
	 * @return void
	 */
	public function testStoreDuplicateEmail()
	{
		User::factory()->count(1)->create([
			'email' => 'test@test.com'
		]);
		
		$this->post(route('user.store'), [
			'cellphone'				=> '09192332861',
			'email'					=> 'test@test.com',
			'password'				=> '123456',
			'password_confirmation'	=> '123456',
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	/**
	 * test duplicate cellphone
	 * 
	 * @return void
	 */
	public function testStoreDuplicateCellphone()
	{
		User::factory()->count(1)->create([
			'cellphone' => '091233665544'
		]);
		$this->post(route('user.store'), [
			'cellphone'				=> '091233665544',
			'email'					=> 'test@test.com',
			'password'				=> '123456',
			'password_confirmation'	=> '123456',
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	/**
	 * test duplicate cellphone
	 * 
	 * @return void
	 */
	public function testVerifyUserCellphone()
	{
		$userId				= 6;
		$verificationCode	= Str::randomDigits(6);
		User::factory()->count(1)->create([
			'id'					=> $userId,
			'cellphone'				=> '091233665544',
			'cellphone_verified_at'	=> null
		]);
		
		Verify::factory()->count(1)->create([
			'user_id'			=> $userId,
			'verification_code'	=> $verificationCode,
			'expiration_date'	=> now()->timestamp+ config('verification.verification-expiration-seconds')
		]);

		$this->post(route('user.verify.cellphone', ['user'=>$userId]), [
			'verification_code'	=> $verificationCode,
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		;
		
		$userModel		= new User();
		$this->assertDatabaseHas($userModel->getTable(), [
			'id'					=> $userId,
			'cellphone_verified_at'	=> now()->timestamp
		]);
	}

	public function testInvalidVerifyUserCellphone()
	{
		$userId				= 6;
		$verificationCode	= Str::randomDigits(6);
		User::factory()->count(1)->create([
			'id'					=> $userId,
			'cellphone'				=> '091233665544',
			'cellphone_verified_at'	=> null
		]);
		
		Verify::factory()->count(1)->create([
			'user_id'			=> $userId,
			'verification_code'	=> $verificationCode.'123',
			'expiration_date'	=> now()->timestamp+ config('verification.verification-expiration-seconds')
		]);

		$this->post(route('user.verify.cellphone', ['user'=>$userId]), [
			'verification_code'	=> $verificationCode,
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_FAILED_DEPENDENCY)
		;
	}
	
	public function testExpiredVerificationCode()
	{
		$userId				= 6;
		$verificationCode	= Str::randomDigits(6);
		User::factory()->count(1)->create([
			'id'					=> $userId,
			'cellphone'				=> '091233665544',
			'cellphone_verified_at'	=> null
		]);

		Verify::factory()->count(1)->create([
			'user_id'			=> $userId,
			'verification_code'	=> $verificationCode,
			'expiration_date'	=> now()->timestamp-10
		]);

		$this->post(route('user.verify.cellphone', ['user'=>$userId]), [
			'verification_code'	=> $verificationCode,
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
		;
	}
	
	public function testResendVerifivationCode()
	{
		$userId				= 6;
		$verificationCode	= Str::randomDigits(6);
		User::factory()->count(1)->create([
			'id'					=> $userId,
			'cellphone'				=> '091233665544',
			'cellphone_verified_at'	=> null
		]);

		Verify::factory()->count(1)->create([
			'user_id'			=> $userId,
			'verification_code'	=> $verificationCode,
			'expiration_date'	=> now()->timestamp-10
		]);

		$this->post(route('user.resend.verifycode', ['user'=>$userId]), [],
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		;
		
		$verifyModel	= new Verify();
		$this->assertDatabaseHas($verifyModel->getTable(), [
			'user_id'	=> $userId
		]);
	}
	
	public function testRemainingResendVerifivationCode()
	{
		$userId				= 6;
		$verificationCode	= Str::randomDigits(6);
		User::factory()->count(1)->create([
			'id'					=> $userId,
			'cellphone'				=> '091233665544',
			'cellphone_verified_at'	=> null
		]);

		Verify::factory()->count(1)->create([
			'user_id'			=> $userId,
			'verification_code'	=> $verificationCode,
			'expiration_date'	=> now()->timestamp+30
		]);

		$this->post(route('user.resend.verifycode', ['user'=>$userId]), [],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_NOT_ACCEPTABLE)
		->assertJsonStructure([
			'data'	=> [
				'remainTime'
			]
		])
		->assertJson([
			'data'	=> [
				'remainTime'	=> 30
			]
		])
		;
	
	}
	
	public function testSendVerificationCodeViaEmail()
	{
		//Overwirte send verification method to email
		config()->set('verification.verification-method', 'Email');
		
		$this->post(route('user.store'), [
			'cellphone'				=> '09192332269',
			'email'					=> 'babak.yahel@gmail.com',
			'password'				=> '123456',
			'password_confirmation'	=> '123456',
		],
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->assertJson([
			'data' => [
				'id' => 1
			]
		])
		;
		
		$userModel		= new User();
		$verifyModel	= new Verify();
		$this->assertDatabaseHas($userModel->getTable(), [
			'id'		=> 2,
			'cellphone'	=> '09192332269',
			'email'		=> 'babak.yahel@gmail.com',
		]);
		$this->assertDatabaseHas($verifyModel->getTable(), [
			'user_id'	=> 1
		]);
	}
}
