<?php
namespace Tests\Feature\Invoice;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\AuditLog\AuditLog;
use App\Models\Invoice\Invoice;
use App\Models\Bank\Bank;
use Carbon\Carbon;

class InvoiceControllerTest extends TestCase
{
	use RefreshDatabase;
	
	public function testManualPayInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$amount		= 43.2;
		$this->userActingAs([
			'user_type'	=> ADMIN_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> $amount
		]);
		
		$this->post(route('invoice.pay', [
				'payType'	=> 'Manual',
				'invoice'	=> $invoiceId
			]),
			[
				'tracking_code'	=> 'kasdjjasdhkadhskasdj'
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$invoice	= new Invoice();
		$bank		= new Bank();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> $amount,
			'user_id'	=> $userId,
			'pay_type'	=> 'Manual',
			'payed_at'	=> Carbon::now()->timestamp,
		]);
		$this->assertDatabaseHas($bank->getTable(), [
			'user_id'	=> $userId,
			'amount'	=> $amount
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::BANK_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> $amount,
			]);
		}
	}
	
	public function testManualPayAddToBankInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$amount		= 43.2;
		$this->userActingAs([
			'user_type'	=> ADMIN_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> $amount
		]);
		
		Bank::factory()->create([
			'user_id'		=> $userId,
			'amount'		=> 456
		]);
		
		$this->post(route('invoice.pay', [
				'payType'	=> 'Manual',
				'invoice'	=> $invoiceId
			]),
			[
				'tracking_code'	=> 'kasdjjasdhkadhskasdj'
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$invoice	= new Invoice();
		$bank		= new Bank();
		$this->assertDatabaseHas($invoice->getTable(), [
			'amount'	=> $amount,
			'user_id'	=> $userId,
			'pay_type'	=> 'Manual',
			'payed_at'	=> Carbon::now()->timestamp,
		]);
		$this->assertDatabaseHas($bank->getTable(), [
			'user_id'	=> $userId,
			'amount'	=> $amount+456
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $invoiceId,
				'user_id'		=> $userId,
				'resource'		=> AuditLogAbstract::BANK_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> $amount,
			]);
		}
	}
	
	public function testManualRepayInvoice()
	{
		$userId		= 5;
		$invoiceId	= 8;
		$amount		= 43.2;
		$this->userActingAs([
			'user_type'	=> ADMIN_USER,
			'id'		=> $userId
		]);
		
		Invoice::factory()->create([
			'invoice_id'	=> $invoiceId,
			'user_id'		=> $userId,
			'amount'		=> $amount,
			'payed_at'		=> Carbon::now()->timestamp
		]);
		
		$this->post(route('invoice.pay', [
				'payType'	=> 'Manual',
				'invoice'	=> $invoiceId
			]),
			[
				'tracking_code'	=> 'kasdjjasdhkadhskasdj'
			],
			[
				'Accept'	=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
}