<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\Users\User;
use App\Models\Users\Verify;

class FolkControllerTest extends TestCase
{
	use RefreshDatabase;
	
	/**
	 * store new user required data
	 *
	 * @return void
	 */
	public function testStoreFolk()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		
		$this->post(route('folk.store'), [
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
				'id' => 2
			]
		])
		;
		
		$userModel		= new User();
		$verifyModel	= new Verify();
		$this->assertDatabaseHas($userModel->getTable(), [
			'id'		=> 2,
			'cellphone'	=> '09192332269',
			'email'		=> 'test@sdfsdf.sdf',
		]);
		$this->assertDatabaseHas($verifyModel->getTable(), [
			'user_id'	=> 2
		]);
	}
}
