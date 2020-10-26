<?php
namespace Tests\Feature\Monitoring;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonitoringControllerTest extends TestCase
{
	use RefreshDatabase;
	
	public function testGetTemplatesList()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);

		$this->get(route('monitoring.template.list'),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data' => [
				'*'	=> [
					'templateid',
					'name'
				]
			]
		])
		;
	}
}