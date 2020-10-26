<?php
namespace Tests\Feature\Shopping;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Helpers\PaginateHelper;
use App\Models\Product\Product;
use App\Models\Bank\Bank;
use App\Models\Product\ShoppingCart;
use App\Models\Product\ShoppingCartItem;
use App\Models\Bank\PaymentLog;
use App\Models\Product\UserInventory;
use App\Models\Package\Package;
use App\Models\Package\PackageItems;

class ShoppingControllerTest extends TestCase
{
	use RefreshDatabase;
	
	public function testPaymentLogsList()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		PaymentLog::factory()->count(20)->create([
			'user_id'		=> $userId,
			'price'			=> 100,
		]);
		$this->get(route('shopping.index' ,[PaginateHelper::RECORD_COUNT => 50,]), ['Accept' => 'application/json'])
			->assertOk()
			->assertJsonStructure([
				'data' => [
					'data'	=> [
						'*'	=> [
							'payment_log_id',
							'user_id',
							'price',
							'entity_id',
							'pay_for',
							'created_at',
							'updated_at'
						]
					],
					'current_page',
					'from',
					'per_page',
					'to',
					'total'
				]
			])
			->assertJson([
				'data' => [
					'data'	=> [
						[
							'user_id'	=> $userId,
						]
					],
				]
			])
			;
	}
	
	public function testBuyProductWithBasket()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);

		Bank::factory()->create([
			'user_id'	=> $userId,
			'amount'	=> 1000
		]);
		
		Product::factory()->create([
			'product_id'	=> 1,
			'product_type'	=> SMS,
			'price'			=> 10,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 2,
			'product_type'	=> ITEM,
			'price'			=> 20,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 3,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'price'			=> 30,
			'entity_id'		=> 16
		]);
		
		$content	= $this->post(route('shopping.store'),
			[
				'products'	=> [
					[
						'product_id'	=> 1,
						'product_count'	=> 10
					],
					[
						'product_id'	=> 2,
						'product_count'	=> 20
					],
					[
						'product_id'	=> 3,
						'product_count'	=> 1
					],
				]
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->getContent()
		;
		
		$content	= json_decode($content, true);
		
		$shoppingCart		= new ShoppingCart();
		$shoppingCartItem	= new ShoppingCartItem();
		$bank				= new Bank();
		$paymentLog			= new PaymentLog();
		$userInventory		= new UserInventory();
		//shopping cart
		$this->assertDatabaseHas($shoppingCart->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'user_id'			=> $userId,
			'total_price'		=> 530
		]);
		
		//shopping cart items
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 1,
			'product_count'		=> 10,
			'price'				=> 10,
			'total_price'		=> 100,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 2,
			'product_count'		=> 20,
			'price'				=> 20,
			'total_price'		=> 400,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 3,
			'product_count'		=> 1,
			'price'				=> 30,
			'total_price'		=> 30,
		]);
		
		//User account balance
		$this->assertDatabaseHas($bank->getTable(), [
			'user_id'	=> $userId,
			'amount'	=> 470
		]);
		
		//Payment log record
		$this->assertDatabaseHas($paymentLog->getTable(), [
			'user_id'	=> $userId,
			'price'		=> 530,
			'entity_id'	=> $content['data']['id'],
			'pay_for'	=> config('payment.pay-for.buy-product')
		]);
		
		//User inventories
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 1,
			'product_count'	=> 10,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 2,
			'product_count'	=> 20,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 3,
			'product_count'	=> 1,
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> $content['data']['id'],
			'user_id'		=> $userId,
			'resource'		=> AuditLogAbstract::SHOPPIN_CART_RESOURCE,
			'action'		=> AuditLogAbstract::INSERT_ACTION,
		]);
	}
	
	public function testBuyProductWithBasketAndExistProduct()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);

		Bank::factory()->create([
			'user_id'	=> $userId,
			'amount'	=> 1000
		]);
		
		Product::factory()->create([
			'product_id'	=> 1,
			'product_type'	=> SMS,
			'price'			=> 10,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 2,
			'product_type'	=> ITEM,
			'price'			=> 20,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 3,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'price'			=> 30,
			'entity_id'		=> 16
		]);
		
		UserInventory::factory()->create([
			'product_id'	=> 1,
			'user_id'		=> $userId,
			'product_count'	=> 10,
		]);
		
		$content	= $this->post(route('shopping.store'),
			[
				'products'	=> [
					[
						'product_id'	=> 1,
						'product_count'	=> 10
					],
					[
						'product_id'	=> 2,
						'product_count'	=> 20
					],
					[
						'product_id'	=> 3,
						'product_count'	=> 1
					],
				]
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->getContent()
		;
		
		$content			= json_decode($content, true);
		$shoppingCart		= new ShoppingCart();
		$shoppingCartItem	= new ShoppingCartItem();
		$bank				= new Bank();
		$paymentLog			= new PaymentLog();
		$userInventory		= new UserInventory();
		
		//shopping cart
		$this->assertDatabaseHas($shoppingCart->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'user_id'			=> $userId,
			'total_price'		=> 530
		]);
		
		//shopping cart items
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 1,
			'product_count'		=> 10,
			'price'				=> 10,
			'total_price'		=> 100,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 2,
			'product_count'		=> 20,
			'price'				=> 20,
			'total_price'		=> 400,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 3,
			'product_count'		=> 1,
			'price'				=> 30,
			'total_price'		=> 30,
		]);
		
		//User account balance
		$this->assertDatabaseHas($bank->getTable(), [
			'user_id'	=> $userId,
			'amount'	=> 470
		]);
		
		//Payment log record
		$this->assertDatabaseHas($paymentLog->getTable(), [
			'user_id'	=> $userId,
			'price'		=> 530,
			'entity_id'	=> $content['data']['id'],
			'pay_for'	=> config('payment.pay-for.buy-product')
		]);
		
		//User inventories
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 1,
			'product_count'	=> 20,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 2,
			'product_count'	=> 20,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 3,
			'product_count'	=> 1,
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> $content['data']['id'],
			'user_id'		=> $userId,
			'resource'		=> AuditLogAbstract::SHOPPIN_CART_RESOURCE,
			'action'		=> AuditLogAbstract::INSERT_ACTION,
		]);
	}
	
	public function testBuyProductWithDuplicatedPermanentProduct()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);

		Bank::factory()->create([
			'user_id'	=> $userId,
			'amount'	=> 1000
		]);
		
		Product::factory()->create([
			'product_id'	=> 1,
			'product_type'	=> SMS,
			'price'			=> 10,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 2,
			'product_type'	=> ITEM,
			'price'			=> 20,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 3,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'price'			=> 30,
			'entity_id'		=> 16
		]);
		
		UserInventory::factory()->create([
			'product_id'	=> 3,
			'user_id'		=> $userId,
			'product_count'	=> 1,
		]);
		
		$this->post(route('shopping.store'),
			[
				'products'	=> [
					[
						'product_id'	=> 1,
						'product_count'	=> 10
					],
					[
						'product_id'	=> 2,
						'product_count'	=> 20
					],
					[
						'product_id'	=> 3,
						'product_count'	=> 1
					],
				]
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
		;
	}
	
	public function testBuyProductNotEnoughAcountBalance()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);

		Bank::factory()->create([
			'user_id'	=> $userId,
			'amount'	=> 10
		]);
		
		Product::factory()->create([
			'product_id'	=> 1,
			'product_type'	=> SMS,
			'price'			=> 10,
			'product_cat'	=> COUNTABLE
		]);
		
		$this->post(route('shopping.store'),
			[
				'products'	=> [
					[
						'product_id'	=> 1,
						'product_count'	=> 10
					]
				]
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_PAYMENT_REQUIRED)
		;
	}
	
	public function testBuyPackage()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);

		Bank::factory()->create([
			'user_id'	=> $userId,
			'amount'	=> 2000
		]);
		
		Product::factory()->create([
			'product_id'	=> 1,
			'product_type'	=> SMS,
			'price'			=> 10,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 2,
			'product_type'	=> ITEM,
			'price'			=> 20,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 3,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'price'			=> 30,
			'entity_id'		=> 16
		]);
		
		$packageId			= 1;
		
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 1000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 10,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 2,
			'count'			=> 20,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 3,
			'count'			=> 1,
		]);
		
		$content	= $this->post(route('shopping.buypackage', ['package'=>$packageId]),
			[
				'package'	=> $packageId
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->getContent()
		;
		
		$content			= json_decode($content, true);
		$shoppingCart		= new ShoppingCart();
		$shoppingCartItem	= new ShoppingCartItem();
		$bank				= new Bank();
		$paymentLog			= new PaymentLog();
		$userInventory		= new UserInventory();
		//shopping cart
		$this->assertDatabaseHas($shoppingCart->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'user_id'			=> $userId,
			'total_price'		=> 1000
		]);
		
		//shopping cart items
		
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 1,
			'product_count'		=> 10,
			'price'				=> 0,
			'total_price'		=> 0,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 2,
			'product_count'		=> 20,
			'price'				=> 0,
			'total_price'		=> 0,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 3,
			'product_count'		=> 1,
			'price'				=> 0,
			'total_price'		=> 0,
		]);
		
		//User account balance
		
		$this->assertDatabaseHas($bank->getTable(), [
			'user_id'	=> $userId,
			'amount'	=> 1000
		]);
		
		//Payment log record
		
		$this->assertDatabaseHas($paymentLog->getTable(), [
			'user_id'	=> $userId,
			'price'		=> 1000,
			'entity_id'	=> $content['data']['id'],
			'pay_for'	=> config('payment.pay-for.buy-package')
		]);
		
		//User inventories
		
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 1,
			'product_count'	=> 10,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 2,
			'product_count'	=> 20,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 3,
			'product_count'	=> 1,
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> $content['data']['id'],
			'user_id'		=> $userId,
			'resource'		=> AuditLogAbstract::SHOPPIN_CART_RESOURCE,
			'action'		=> AuditLogAbstract::INSERT_ACTION,
		]);
	}
	
	public function testBuyPackageWithExistingInventories()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);

		Bank::factory()->create([
			'user_id'	=> $userId,
			'amount'	=> 2000
		]);
		
		Product::factory()->create([
			'product_id'	=> 1,
			'product_type'	=> SMS,
			'price'			=> 10,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 2,
			'product_type'	=> ITEM,
			'price'			=> 20,
			'product_cat'	=> COUNTABLE
		]);
		
		Product::factory()->create([
			'product_id'	=> 3,
			'product_type'	=> TEMPLATE,
			'product_cat'	=> PERMANENT,
			'price'			=> 30,
			'entity_id'		=> 16
		]);
		
		UserInventory::factory()->create([
			'product_id'	=> 1,
			'user_id'		=> $userId,
			'product_count'	=> 10,
		]);
		
		UserInventory::factory()->create([
			'product_id'	=> 2,
			'user_id'		=> $userId,
			'product_count'	=> 15,
		]);
		
		UserInventory::factory()->create([
			'product_id'	=> 3,
			'user_id'		=> $userId,
			'product_count'	=> 1,
		]);
		
		$packageId			= 1;
		
		Package::factory()->create([
			'package_id'	=> $packageId,
			'title'			=> 'dollar',
			'price'			=> 1000,
			'status'		=> 1,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 1,
			'count'			=> 10,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 2,
			'count'			=> 20,
		]);
		PackageItems::factory()->create([
			'product_id'	=> 3,
			'count'			=> 1,
		]);
		
		$content	= $this->post(route('shopping.buypackage', ['package'=>$packageId]),
			[
				'package'	=> $packageId
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data' => [
				'id'
			]
		])
		->getContent()
		;
		
		$content			= json_decode($content, true);
		$shoppingCart		= new ShoppingCart();
		$shoppingCartItem	= new ShoppingCartItem();
		$bank				= new Bank();
		$paymentLog			= new PaymentLog();
		$userInventory		= new UserInventory();
		//shopping cart
		$this->assertDatabaseHas($shoppingCart->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'user_id'			=> $userId,
			'total_price'		=> 1000
		]);
		
		//shopping cart items
		
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 1,
			'product_count'		=> 10,
			'price'				=> 0,
			'total_price'		=> 0,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 2,
			'product_count'		=> 20,
			'price'				=> 0,
			'total_price'		=> 0,
		]);
		$this->assertDatabaseHas($shoppingCartItem->getTable(), [
			'shopping_cart_id'	=> $content['data']['id'],
			'product_id'		=> 3,
			'product_count'		=> 1,
			'price'				=> 0,
			'total_price'		=> 0,
		]);
		
		//User account balance
		
		$this->assertDatabaseHas($bank->getTable(), [
			'user_id'	=> $userId,
			'amount'	=> 1000
		]);
		
		//Payment log record
		
		$this->assertDatabaseHas($paymentLog->getTable(), [
			'user_id'	=> $userId,
			'price'		=> 1000,
			'entity_id'	=> $content['data']['id'],
			'pay_for'	=> config('payment.pay-for.buy-package')
		]);
		
		//User inventories
		
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 1,
			'product_count'	=> 20,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 2,
			'product_count'	=> 35,
		]);
		$this->assertDatabaseHas($userInventory->getTable(), [
			'user_id'		=> $userId,
			'product_id'	=> 3,
			'product_count'	=> 1,
		]);
		
		$this->assertAuditLog([
			'entity_id'		=> $content['data']['id'],
			'user_id'		=> $userId,
			'resource'		=> AuditLogAbstract::SHOPPIN_CART_RESOURCE,
			'action'		=> AuditLogAbstract::INSERT_ACTION,
		]);
	}
}
