<?php
namespace Tests\Feature\Inventory;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Helpers\PaginateHelper;
use App\Models\Product\UserInventory;

class InventoryControllerTest extends TestCase
{
	use RefreshDatabase;
	
	public function testUserInventoriesList()
	{
		$userId		= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		UserInventory::factory()->count(100)->create([
			'user_id'		=> $userId,
		]);

		$this->get(route('inventory.index', [
				'productType'					=> [1,2],
				PaginateHelper::RECORD_COUNT	=> 50,
			]),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data' => [
				'current_page',
				'data'	=> [
					'*'	=> [
						'inventory_id',
						'user_id',
						'product_id',
						'product_count',
						'created_at',
					]
				],
				'total',
				'per_page'
			]
		])
		;
	}

	public function testUserAllInventoriesList()
	{
		$userId		= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		UserInventory::factory()->count(100)->create([
			'user_id'		=> $userId,
		]);

		$this->get(route('inventory.index', [
				'all'	=> 'all'
			]),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data' => [
				'*'	=> [
					'inventory_id',
					'user_id',
					'product_id',
					'product_count',
					'created_at',
				]
			]
		])
		;
	}
	
	public function testUserInventoriesProductTypeList()
	{
		$userId		= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		UserInventory::factory()->count(100)->create([
			'user_id'		=> $userId,
		]);

		$this->get(route('inventory.index', [
				'productType'					=> [1,2],
				PaginateHelper::RECORD_COUNT	=> 1,
			]),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data' => [
				'current_page',
				'data'	=> [
					'*'	=> [
						'inventory_id',
						'user_id',
						'product_id',
						'product_count',
						'created_at',
					]
				],
				'total',
				'per_page'
			]
		])
		;
	}
}
