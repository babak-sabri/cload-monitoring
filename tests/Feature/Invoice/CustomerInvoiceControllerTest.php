<?php
namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\AuditLog\AuditLog;
use App\Models\Invoice\Invoice;
use App\Helpers\PaginateHelper;

class CustomerInvoiceControllerTest extends TestCase
{
	use RefreshDatabase;
	
	public function testStoreInvoice()
	{
		$userId	= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		$content	= $this->post(route('invoice.store'),
			[
				'amount'		=> 8.36, //Dolar Price
				'description'	=> 'test description'
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
		$invoice	= new Invoice();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'		=> 8.36, //Dolar Price
			'description'	=> 'test description',
			'user_id'		=> $userId
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$content	= \GuzzleHttp\json_decode($content, true);
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $content['data']['id'],
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::INSERT_ACTION,
				'description'	=> '8.36',
			]);
		}
	}
	
	public function testUpdateInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'description'	=> 'test description',
			'amount'		=> 456,
		]);
		
		$data	= [
			'amount'		=> 8.36, //Dolar Price,
			'description'	=> 'test description 2'
		];

		$this->put(route('invoice.update', ['invoice' => $invoiceId]),
			$data,
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertOk()
		->getContent()
		;
		$invoice	= new Invoice();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> 8.36, //Dolar Price
			'description'	=> 'test description 2',
			'user_id'	=> $userId
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> '8.36',
			]);
		}
	}
	
	public function testUpdatePayedInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> 456,
			'payed_at'		=> 4567
		]);
		
		$data	= [
			'amount'	=> 8.36, //Dolar Price
		];

		$this->put(route('invoice.update', ['invoice' => $invoiceId]),
			$data,
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_LOCKED)
		;
		$invoice	= new Invoice();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> 456, //Dolar Price
			'user_id'	=> $userId
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseMissing($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> '8.36',
			]);
		}
	}
	
	public function testUpdateOtherUserInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> 6,
			'amount'		=> 456
		]);
		
		$data	= [
			'amount'	=> 8.36, //Dolar Price
		];

		$this->put(route('invoice.update', ['invoice' => $invoiceId]),
			$data,
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
		$invoice	= new Invoice();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> 456, //Dolar Price
			'user_id'	=> 6
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseMissing($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> '8.36',
			]);
		}
	}
	
	public function testDeleteInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> 456.7
		]);

		$this->delete(route('invoice.delete', ['invoice' => $invoiceId]),
			[],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$invoice	= new Invoice();
		$this->assertDatabaseMissing($invoice->getTable(), [
			'amount'	=> 456, //Dolar Price
			'user_id'	=> 6
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::DELETE_ACTION,
				'description'	=> '456.7',
			]);
		}
	}
	
	public function testDeletePayedInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> 456,
			'payed_at'		=> 4567
		]);

		$this->delete(route('invoice.delete', ['invoice' => $invoiceId]),
			[],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_LOCKED)
		;
		$invoice	= new Invoice();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> 456, //Dolar Price
			'user_id'	=> $userId
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseMissing($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> '8.36',
			]);
		}
	}
	
	public function testDeleteOtherUserInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> 6,
			'amount'		=> 456
		]);

		$this->delete(route('invoice.delete', ['invoice' => $invoiceId]),
			[],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
		$invoice	= new Invoice();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> 456, //Dolar Price
			'user_id'	=> 6
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseMissing($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::INVOICE_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> '8.36',
			]);
		}
	}

	public function testShowInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> 456
		]);

		$this->get(route('invoice.show', ['invoice' => $invoiceId]),
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data' => [
				'invoice_id',
				'amount',
				'user_id',
				'payed_at',
				'pay_type',
				'tracking_code',
				'updated_at',
				'created_at',
			]
		])
		;
	}
	
	public function testShowOtherUserInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> 6,
			'amount'		=> 456
		]);

		$this->get(route('invoice.show', ['invoice' => $invoiceId]),
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
	
	public function testInvoicesList()
	{
		$userId		= 5;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->count(10)->create([
			'user_id'		=> 2,
			'amount'		=> 456
		]);
		Invoice::factory()->count(3)->create([
			'user_id'		=> $userId,
			'amount'		=> 456
		]);

		$this->get(route('invoice.index', [
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
					[
						'invoice_id',
						'amount',
						'user_id',
						'payed_at',
						'pay_type',
						'tracking_code',
						'updated_at',
						'created_at'
					]
				],
				'total',
				'per_page'
			]
		])
		->assertJson([
			'data' => [
				'data'	=> [
					[
						'user_id'	=> 5
					]
				]
			]
		])
		;
	}
}
