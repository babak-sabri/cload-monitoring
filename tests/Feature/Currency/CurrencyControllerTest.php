<?php
namespace Tests\Feature\Currency;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Currency\Currency;

class CurrencyControllerTest extends TestCase
{
	use RefreshDatabase;
	
	
	public function testCreateCurrency()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		
		$data	= [
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
		];
		$this->post(route('currency.create'), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data'	=> [
				'id'
			]
		])
		->assertJson([
			'data'	=> [
				'id'	=> 1
			]
		])
		;
		
		$this->assertDatabaseHas('currencies', $data);
		
	}
	
	public function testCreateCurrencyWithNotAdminUser()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		
		$data	= [
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
		];
		$this->post(route('currency.create'), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
	
	
	public function testCreateCurrencyWithDuplicateCurrencyName()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);

		$data	= [
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
		];
		$this->post(route('currency.create'), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	public function testUpdateCurrency()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$data	= [
			'currency_title'			=> 'ریال',
			'currency_price'			=> 200080,
		];

		$this->put(route('currency.update',['currency' => 1]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		;
	}
	public function testUpdateCurrencyWithNotAdminUser()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		Currency::factory()->create([
            'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$data	= [
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
		];
		$this->put(route('currency.update',['currency' => 1]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
	
	public function testUpdateCurrencyWithoutCurrencyName()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$data	= [
			'currency_title'			=> '',
			'currency_price'			=> 20000,
		];
		$this->put(route('currency.update',['currency' => 1]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	public function testUpdateCurrencyWithDuplicateCurrencyName()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
			'currency_id'				=> 1,
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		Currency::factory()->create([
			'currency_title'			=> 'pound',
			'currency_title'			=> 20000,
        ]);
		$data	= [
			'currency_title'			=> 'pound',
			'currency_title'			=> 20000
		];
		$this->put(route('currency.update',['currency' => 1]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	public function testUpdateCurrencyWithOwnDuplicateCurrencyName()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
			'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$data	= [
			'currency_title'			=> 'dollar',
		];
		$this->put(route('currency.update',['currency' => 1]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		;
	}
	
	public function testUpdateCurrencyWithoutPrice()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
            'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$data	= [
			'currency_title'			=> 'dollar',
			'currency_price'			=> '',
		];
		$this->put(route('currency.update',['currency' => 1]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	public function testDeleteCurrency()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
            'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$this->delete(route('currency.destroy',['currency' => 1]),
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		;
	}
	
	public function testDeleteCurrencyWithNotAdminUser()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		Currency::factory()->create([
            'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$this->delete(route('currency.destroy',['currency' => 1]),
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
	
	public function testShowCurrency()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		Currency::factory()->create([
            'currency_title'			=> 'dollar',
			'currency_price'			=> 20000,
        ]);
		$this->get(route('currency.show',['currency' => 1]),
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		->assertJsonStructure([
			'data'=> [
				'currency_id',
				'currency_title',
				'currency_price',
			]
		])
		;
	}
	
}