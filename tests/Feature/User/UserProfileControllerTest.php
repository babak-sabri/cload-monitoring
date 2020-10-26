<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\Users\User;


class UserProfileControllerTest extends TestCase
{
	use RefreshDatabase;
	
	/**
	 * get user profile
	 *
	 * @return void
	 */
	public function testGetUserProfile()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$this->get(route('profile.show'),
		[
			'Content-Type'	=> 'application/json',
		])
		->assertOk()
		->assertJson([
			'data' => [
				'id' => 1,
				'user_type'	=> CUSTOMER_USER
			]
		])
		->assertJsonStructure([
			'data' => [
				'id',
				'first_name',
				'last_name',
				'email',
				'cellphone',
				'job_title',
				'gender',
				'language',
				'calendar_type',
				'user_type',
				'timezone',
				'profile_image',
				'how_to_find',
				'email_verified_at',
				'cellphone_verified_at',
				'expiration_date',
				'created_at',
				'updated_at',
			]
		])
		;
	}
	
	/**
	 * update user profile
	 *
	 * @return void
	 */
	public function testUpdateUserProfile()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$data	= [
			'first_name'	=> 'first name',
			'last_name'		=> 'last name',
			'gender'		=> FEMALE_GENDER,
			'organization'	=> 'organization name',
			'job_title'		=> 'test job title',
			'calendar_type'	=> GREGORIAN_CALENDAR,
			'language'		=> ENGLISH_LANGUAGE,
			'how_to_find'	=> 'test how to find',
			'timezone'		=> 'Asia/Tehran'
		];
		$this->put(route('profile.update'), $data)
		->assertOk()
		;
		
		$this->get(route('profile.show'),
		[
			'Content-Type'	=> 'application/json',
		])
		->assertOk()
		->assertJson([
			'data' => $data
		])
		->assertJsonStructure([
			'data' => [
				'id',
				'first_name',
				'last_name',
				'email',
				'cellphone',
				'job_title',
				'gender',
				'language',
				'calendar_type',
				'user_type',
				'timezone',
				'profile_image',
				'how_to_find',
				'email_verified_at',
				'cellphone_verified_at',
				'expiration_date',
				'created_at',
				'updated_at',
			]
		])
		;
		
		
		$userModel		= new User();
		$this->assertDatabaseHas($userModel->getTable(), $data);
		
	}
	
	/**
	 * update user profile
	 *
	 * @return void
	 */
	public function testChangePassword()
	{
		$this->userActingAs([
			'cellphone'	=> '09192332841',
			'password'	=> Hash::make('testpassword'),
			'user_type'	=> CUSTOMER_USER
		]);
		$data	= [
			'old_password'				=> 'testpassword',
			'new_password'				=> 'new_password',
			'new_password_confirmation'	=> 'new_password',
		];
		
		$this->patch(route('profile.changePassword'), $data, [
		])
		->assertOk()
		;
	}
}
