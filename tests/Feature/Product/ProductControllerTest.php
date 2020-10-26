<?php
namespace Tests\Feature\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\Product\Product;
use App\Helpers\PaginateHelper;

class ProductControllerTest extends TestCase
{
	use RefreshDatabase;
	
	public function testProductsList()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);

		Product::factory()->count(20)->create([
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		]);
		$this->get(
			route('product.index', [
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
						'product_id',
						'title',
						'description',
						'price',
						'product_type',
						'product_cat'
					]
				],
				'total',
				'per_page'
			]
		])
		;
	}

	public function testAllProductsList()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);

		Product::factory()->count(20)->create([
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		]);
		
		$this->get(
			route('product.index', [
				'all'	=> 'all',
				PaginateHelper::RECORD_COUNT	=> 50,
			]),
			['Accept'	=> 'application/json']
		)
		->assertOK()
		->assertJsonStructure([
			'data' => [
				'*'	=> [
					'product_id',
					'title',
					'description',
					'price',
					'product_type',
					'product_cat'
				]
			]
		])
		;
	}
	
	public function testShowProduct()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		
		$data	= [
			'product_id'		=> 1,
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		];
		
		Product::factory()->create($data);
		
		$this->get(
			route('product.show', ['product'=>1]),
			['Accept'	=> 'application/json']
		)
		->assertOK()
		->assertJson([
			'data'	=> $data
		])
		;
	}
	
	public function testStoreProduct()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		
		$data	= [
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		];
		
		$content	= $this->post(
			route('product.store'),
			$data,
			['Accept'	=> 'application/json']
		)
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data'	=> [
				'id'
			]
		])
		->getContent()
		;
		$content	= json_decode($content, true);
		$product	= new Product();
		$this->assertDatabaseHas($product->getTable(), [
			'product_id'	=> $content['data']['id'],
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> $content['data']['id'],
			'user_id'		=> 1,
			'resource'		=> AuditLogAbstract::PRODUCT_RESOURCE,
			'action'		=> AuditLogAbstract::INSERT_ACTION,
		]);
	}
	
	public function testUpdateProduct()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		
		$data	= [
			
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		];
		
		Product::factory()->create([
			'product_id'		=> 1,
			'price'				=> 1000
        ]);
		
		$this->put(
			route('product.update', ['product'=>1]),
			$data,
			['Accept'	=> 'application/json']
		)
		->assertOK()
		;
		$product	= new Product();
		$this->assertDatabaseHas($product->getTable(), [
			'product_id'	=> 1,
			'title'			=> 'test title',
			'description'	=> 'test description',
			'price'			=> 2.3,
			'product_type'	=> SMS,
			'product_cat'	=> COUNTABLE
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> 1,
			'user_id'		=> 1,
			'resource'		=> AuditLogAbstract::PRODUCT_RESOURCE,
			'action'		=> AuditLogAbstract::UPDATE_ACTION,
		]);
	}
	
	public function testDeleteProduct()
	{
		$this->userActingAs([
			'user_type'	=> ADMIN_USER
		]);
		
		Product::factory()->create([
			'product_id'		=> 1,
			'price'				=> 1000
        ]);
		
		$this->delete(
			route('product.update', ['product'=>1]),
			[],
			['Accept'	=> 'application/json']
		)
		->assertOK()
		;
		$product	= new Product();
		$this->assertDatabaseMissing($product->getTable(), [
			'product_id'	=> 1,
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> 1,
			'user_id'		=> 1,
			'resource'		=> AuditLogAbstract::PRODUCT_RESOURCE,
			'action'		=> AuditLogAbstract::DELETE_ACTION,
		]);
	}
}