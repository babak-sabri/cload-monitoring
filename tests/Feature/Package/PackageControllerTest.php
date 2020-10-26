<?php

namespace Tests\Feature\Package;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Package\Package;
use App\Models\Package\PackageItems;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\Product\Product;
use App\Helpers\PaginateHelper;

class PackageControllerTest extends TestCase
{
	use RefreshDatabase;
	
	
	public function testPackagesList()
	{ 
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);

		Package::factory()->count(20)->create([
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
		]);
		
		$this->get(
			route('package.index', [
				PaginateHelper::RECORD_COUNT	=> 50,
			]),
			['Accept'	=> 'application/json']
		)
		->assertOK()
		->assertJsonStructure([
			'data' => [
				'current_page',
				'data'	=> [
					'*'	=> [
						'package_id',
						'title',
						'description',
						'price',
						'status'
					]
				],
				'total',
				'per_page'
			]
		])
		;
	}
	
	public function testPackagesWithItemsList()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);

		Product::factory()->create([
			'title'			=> 'email',
			'product_type'	=> EMAIL,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'item',
			'product_type'	=> ITEM,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'	=> 2,
					'count'			=> 50,
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		
		Package::factory()->count(2)->create([
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
		]);

		$this->get(
			route('package.index', [
				PaginateHelper::RECORD_COUNT	=> 50,
			]),
			['Accept'	=> 'application/json']
		)
		->assertOK()
		->assertJsonStructure([
			'data' => [
				'current_page',
				'data'	=> [
					'*'	=> [
						'package_id',
						'title',
						'description',
						'price',
						'status',
						'product_items'	=> [
							'*'	=> [
								'package_items_id',
								'package_id',
								'product_id',
								'count',
								'entity_id',
							]
						]
					]
				],
				'total',
				'per_page'
			]
		])
		;
	}
	
	public function testCreatePackage()
	{ 
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		
		$productId	= 16;
		Product::factory()->create([
			'product_id'	=> $productId,
			'title'			=> 'sms',
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'	=> $productId,
					'count'			=> 50
				],
			]
		];
		
		$content	= $this->post(route('package.create'), $data,
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
		->getContent()
		;
		$result			= json_decode($content, true);
		$packageId		= $result['data']['id'];
		$package		= new Package();
		$packageItems	= new PackageItems();
		$this->assertDatabaseHas($package->getTable(), [
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		
		$this->assertDatabaseHas($packageItems->getTable(), [
			'package_items_id'	=> 1,
			'product_id'		=> $productId,
			'package_id'		=> $packageId,
			'count'				=> 50
		]);
		$this->assertAuditLog([
			'entity_id'		=> $result['data']['id'],
			'user_id'		=> 1,
			'resource'		=> AuditLogAbstract::PACKAGE_RESOURCE,
			'action'		=> AuditLogAbstract::INSERT_ACTION,
			'description'	=> 'dollar',
		]);
	}
	
	public function testCreatePackageWithMultipleProducts()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		Product::factory()->create([
			'title'			=> 'email',
			'product_type'	=> EMAIL,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'item',
			'product_type'	=> ITEM,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'sms',
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		$productItems	= [
				[
					'product_id'		=> 1,
					'count'				=> 50
				],
				[
					'product_id'		=> 2,
					'count'				=> 50
				],
				[
					'product_id'		=> 3,
					'count'				=> 50
				]
			];
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
			'product_items'	=> $productItems
		];
		$this->post(route('package.create'), $data,
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
		$package		= new Package();
		$packageItems	= new PackageItems();
		$this->assertDatabaseHas($package->getTable(), [
			'package_id'	=> 1,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		
		$this->assertDataBase($packageItems->getTable(), $productItems);
		
	}
	public function testCreatePackageWithDuplicateProductId()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		
		$productItems	= [
				[
					'product_id'		=> 1,
					'count'				=> 50
				],
				[
					'product_id'		=> 1,
					'count'				=> 100
				],
			];
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
			'product_items'	=> $productItems
		];
		$this->post(route('package.create'), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	public function testCreatePackageWithPermanentProductId()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		Product::factory()->create([
			'title'			=> 'template',
			'product_id'	=> TEMPLATE,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'entity_id'		=> 1
		]);
		$productItems	= [
				[
					'product_id'		=> TEMPLATE,
					'count'				=> 50
				],
			];
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
			'product_items'	=> $productItems
		];
		$this->post(route('package.create'), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_CREATED)
		->assertJson([
			'data'	=> [
				'id'	=> 1
			]
		])
		;
		$package		= new Package();
		$packageItems	= new PackageItems();
		$this->assertDatabaseHas($package->getTable(), [
			'package_id'	=> 1,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		$this->assertDataBase($packageItems->getTable(), [
			'product_id'		=> TEMPLATE,
			'count'				=> 1
		]);
	}
	
	public function testCreatePackageWithNotAdminUser()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		
		$productItems	= [
				[
					'product_id'		=> 4,
					'count'				=> 50
				],
			];
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
			'product_items'	=> $productItems
		];
		$this->post(route('package.create'), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
	
	public function testUpdatePackage()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		Product::factory()->create([
			'title'			=> 'email',
			'product_type'	=> EMAIL,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'item',
			'product_type'	=> ITEM,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'	=> 2,
					'count'			=> 50,
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		
		$this->put(route('package.update',['package' => $packageId]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		->getContent()
		;
		$package		= new Package();
		$packageItems	= new PackageItems();
		$this->assertDatabaseHas($package->getTable(), [
			'package_id'	=> $packageId,
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
		]);
		$this->assertDatabaseHas($packageItems->getTable(), [
			'product_id'		=> 2,
			'package_id'		=> $packageId,
			'count'				=> 50
		]);
		$this->assertAuditLog([
			'entity_id'		=> $packageId,
			'user_id'		=> 1,
			'resource'		=> AuditLogAbstract::PACKAGE_RESOURCE,
			'action'		=> AuditLogAbstract::UPDATE_ACTION,
		]);
	}
	
	public function testUpdateNamePackage()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		Product::factory()->create([
			'title'			=> 'email',
			'product_type'	=> EMAIL,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'item',
			'product_type'	=> ITEM,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'	=> 2,
					'count'			=> 50,
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		
		$this->put(route('package.update',['package' => $packageId]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		->getContent()
		;
		$package		= new Package();
		$packageItems	= new PackageItems();
		$this->assertDatabaseHas($package->getTable(), [
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 50000,
			'status'		=> 1,
		]);
		$this->assertDatabaseHas($packageItems->getTable(), [
			'product_id'		=> 2,
			'package_id'		=> $packageId,
			'count'				=> 50
		]);
		$this->assertAuditLog([
			'entity_id'		=> $packageId,
			'user_id'		=> 1,
			'resource'		=> AuditLogAbstract::PACKAGE_RESOURCE,
			'action'		=> AuditLogAbstract::UPDATE_ACTION,
		]);
	}

	public function testUpdateDuplicateNamePackage()
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		Product::factory()->create([
			'title'			=> 'email',
			'product_type'	=> EMAIL,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'item',
			'product_type'	=> ITEM,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'dollar',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'	=> 2,
					'count'			=> 50,
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'test name',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		Package::factory()->create([
			'package_id'	=> 2,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		
		$this->put(route('package.update',['package' => $packageId]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	public function testUpdatePackageWithNotAdminUser() 
	{
		$this->userActingAs([
			'user_type'		=> CUSTOMER_USER
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'	=> 2,
					'count'			=> 50,
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		
		$this->put(route('package.update',['package' => $packageId]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_FORBIDDEN)
		->getContent()
		;
	}
	
	public function testUpdatePackageWithPermanentProductId() 
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'		=> 1,
					'count'				=> 50
				],
				[
					'product_id'		=> 1,
					'count'				=> 100
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		
		$this->put(route('package.update',['package' => $packageId]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		->getContent()
		;
	}
	
	public function testUpdatePackageWithDuplicateProductId() 
	{
		$this->userActingAs([
			'user_type'		=> ADMIN_USER
		]);
		Product::factory()->create([
			'product_id'	=> SMS,
			'title'			=> 'email',
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE,
			'entity_id'		=> null
		]);
		Product::factory()->create([
			'title'			=> 'template',
			'product_id'	=> TEMPLATE,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'entity_id'		=> 1
		]);
		$packageId			= 1;
		$data	= [
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
			'product_items'	=> [
				[
					'product_id'		=> TEMPLATE,
					'count'				=> 50
				],
			]
		];
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 20000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 80,
		]);
		$this->put(route('package.update',['package' => $packageId]), $data,
		[
			'Accept'		=> 'application/json',
		])
		->assertOk()
		->getContent()
		;
		$package		= new Package();
		$packageItems	= new PackageItems();
		$this->assertDatabaseHas($package->getTable(), [
			'package_id'	=> $packageId,
			'title'			=> 'euro',
			'price'			=> 50000,
			'status'		=> 1,
		]);
		$this->assertDataBase($packageItems->getTable(), [
			'product_id'	=> TEMPLATE,
			'count'			=> 1,
			'package_id'	=> $packageId,
		]);
	}
	
}