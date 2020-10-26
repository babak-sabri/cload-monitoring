<?php

namespace Tests\Feature\Host;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\HostGroup\HostGroup as HostGroupModel;
use App\Helpers\HostHelper;
use App\Models\Host\Host;
use App\Models\Host\HostGroup;
use App\Models\Host\HostInterface;
use App\Models\Host\HostMacro;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\AuditLog\AuditLog;
use App\Models\Host\HostTemplate;
use App\Helpers\PaginateHelper;
use App\Models\Product\Product;
use App\Models\Product\UserInventory;

class HostControllerTest extends TestCase
{
	use RefreshDatabase;
	private $groupId_1;
	private $groupId_2;
	private $groupId_3;
	private $groupId_4;
	private $templateId_1;
	private $templateId_2;
	private $templateId_3;

	private function createDependencies($userId=1)
	{
		$this->groupId_1	= env('ZABBIX_TESSTING_GROUP_ID_1', 1);
		$this->groupId_2	= env('ZABBIX_TESSTING_GROUP_ID_2', 2);
		$this->groupId_3	= env('ZABBIX_TESSTING_GROUP_ID_3', 3);
		$this->groupId_4	= env('ZABBIX_TESSTING_GROUP_ID_4', 55);
		$this->templateId_1	= env('ZABBIX_TESSTING_TEMPLATE_ID_1', 1);
		$this->templateId_2	= env('ZABBIX_TESSTING_TEMPLATE_ID_2', 2);
		$this->templateId_3	= env('ZABBIX_TESSTING_TEMPLATE_ID_3', 3);

		//Create host groups for test
		HostGroupModel::factory()->create([
			'group_id'			=> $this->groupId_1,
			'user_id'			=> $userId,
		]);
		HostGroupModel::factory()->create([
			'group_id'			=> $this->groupId_2,
			'user_id'			=> $userId,
		]);
		HostGroupModel::factory()->create([
			'group_id'			=> $this->groupId_3,
			'user_id'			=> $userId,
		]);
		HostGroupModel::factory()->create([
			'group_id'			=> $this->groupId_4,
			'user_id'			=> $userId,
		]);
	}
	
	/**
	 * store new host
	 *
	 * @return void
	 */
	public function testHostsList()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		Host::factory()->create([
			'user_id'	=> 2
		]);
		Host::factory()->create([
			'user_id'	=> 1
		]);
		$this->get(route('host.index', [
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
						'hostid',
						'host'
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
						'user_id'	=> 1
					]
				],
			]
		])
		->assertJsonMissing([
			'data' => [
				'data'	=> [
					[
						'user_id'	=> 2
					]
				],
			]
		])
		;
	}

	/**
	 * store new host
	 *
	 * @return void
	 */
	public function testStoreHost()
	{
		$templateProductId	= 16;
		$userId				= 1;
		$this->userActingAs([
			'id'		=> $userId,
			'user_type'	=> CUSTOMER_USER
		]);
		$this->createDependencies($userId);
		
		Product::factory()->create([
			'product_id'	=> $templateProductId,
			'product_type'	=> TEMPLATE,
			'entity_id'		=> $this->templateId_1,
			'product_cat'	=> PERMANENT
		]);
		
		UserInventory::factory()->create([
			'user_id'		=> $userId,
			'product_id'	=> $templateProductId,
			'product_count'	=> 1,
		]);
		
		$hostId		= 18;
		$this->getMonitoring()->host()->mock([
			'hostids'	=> [$hostId]
		]);
		
		$this->post(route('host.store'),
			[
				'host'			=> 'host name',
				'interfaces'	=> [
					[
						'type'	=> HostHelper::AGENT_INTERFAC,
						'main'	=> 1,
						'useip'	=> 1,
						'ip'	=> '192.168.3.1',
						'dns'	=> '',
						'port'	=> '10050'
					],
					[
						'type'	=> HostHelper::SNMP_INTERFACE,
						'main'	=> 1,
						'useip'	=> 0,
						'ip'	=> '',
						'dns'	=> 'www.localhost.local',
						'port'	=> '11050'
					],
				],
				'groups'	=> [
					$this->groupId_1,
					$this->groupId_2
				],
				'templates'	=> [
					$this->templateId_1
				],
				'macros'	=> [
					[
						'macro'	=> HostHelper::AUTHENTICATION_PASSWORD,
						'value'	=> '123321'
					],
					[
						'macro'	=> HostHelper::PRIVACY_PASSWORD,
						'value'	=> '456'
					],
				],
			],
			[
				'Accept'		=> 'application/json',
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
		
		$host			= new Host();
		$hostInterface	= new HostInterface();
		$hostGroup		= new HostGroup();
		$hostMacro		= new HostMacro();
		$hostTemplate	= new HostTemplate();
		$this->assertDatabaseHas($host->getTable(), [
			'hostid'		=> $hostId,
			'host'			=> 'host name',
		]);
		$this->assertDatabaseHas($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> 1,
			'main'		=> 1,
			'useip'		=> 1,
			'ip'		=> '192.168.3.1',
			'port'		=> '10050'
		]);
		$this->assertDatabaseHas($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> HostHelper::SNMP_INTERFACE,
			'main'		=> 1,
			'useip'		=> 0,
			'dns'		=> 'www.localhost.local',
			'port'		=> '11050'
		]);
		$this->assertDatabaseHas($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_1
		]);
		$this->assertDatabaseHas($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_2
		]);
		$this->assertDatabaseHas($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::AUTHENTICATION_PASSWORD,
			'macro_value'	=> '123321'
		]);
		$this->assertDatabaseHas($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::PRIVACY_PASSWORD,
			'macro_value'	=> '456'
		]);
		$this->assertDatabaseHas($hostTemplate->getTable(), [
			'hostid'		=> $hostId,
			'template_id'	=> $this->templateId_1
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $hostId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_RESOURCE,
				'action'		=> AuditLogAbstract::INSERT_ACTION,
				'description'	=> 'host name',
			]);
		}
	}
	
	/**
	 * store new host
	 *
	 * @return void
	 */
	public function testStoreSnmpHost()
	{
		$templateProductId	= 16;
		$userId				= 1;
		$this->userActingAs([
			'id'		=> $userId,
			'user_type'	=> CUSTOMER_USER
		]);
		$this->createDependencies($userId);
		
		Product::factory()->create([
			'product_id'	=> $templateProductId,
			'product_type'	=> TEMPLATE,
			'entity_id'		=> $this->templateId_1,
			'product_cat'	=> PERMANENT
		]);
		
		UserInventory::factory()->create([
			'user_id'		=> $userId,
			'product_id'	=> $templateProductId,
			'product_count'	=> 1,
		]);
		
		$hostId		= 18;
		$this->getMonitoring()->host()->mock([
			'hostids'	=> [$hostId]
		]);
		
		$this->post(route('host.store'),
			[
				'host'			=> 'host name',
				'interfaces'	=> [
					[
						'type'		=> HostHelper::SNMP_INTERFACE,
						'main'		=> 1,
						'useip'		=> 0,
						'ip'		=> '',
						'dns'		=> 'www.localhost.local',
						'port'		=> '11050',
						'details'	=> [
							'version'		=> 3,
							'bulk'			=> 0,
							'securityname'	=> 'mysecurityname',
							'contextname'	=> '',
							'securitylevel'	=> 1
						]
					],
				],
				'groups'	=> [
					$this->groupId_4
				],
				'templates'	=> [
					$this->templateId_1
				],
				'macros'	=> [
					[
						'macro'	=> HostHelper::AUTHENTICATION_PASSWORD,
						'value'	=> '123321'
					],
					[
						'macro'	=> HostHelper::PRIVACY_PASSWORD,
						'value'	=> '456'
					],
				],
			],
			[
				'Accept'		=> 'application/json',
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
		
		$host			= new Host();
		$hostInterface	= new HostInterface();
		$hostGroup		= new HostGroup();
		$hostMacro		= new HostMacro();
		$hostTemplate	= new HostTemplate();
		$this->assertDatabaseHas($host->getTable(), [
			'hostid'		=> $hostId,
			'host'			=> 'host name',
		]);
		$this->assertDatabaseHas($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> HostHelper::SNMP_INTERFACE,
			'main'		=> 1,
			'useip'		=> 0,
			'dns'		=> 'www.localhost.local',
			'port'		=> '11050',
			'details'	=> json_encode([
							'version'		=> 3,
							'bulk'			=> 0,
							'securityname'	=> 'mysecurityname',
							'contextname'	=> null,
							'securitylevel'	=> 1
						])
		]);

		$this->assertDatabaseHas($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_4
		]);
		$this->assertDatabaseHas($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::AUTHENTICATION_PASSWORD,
			'macro_value'	=> '123321'
		]);
		$this->assertDatabaseHas($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::PRIVACY_PASSWORD,
			'macro_value'	=> '456'
		]);
		$this->assertDatabaseHas($hostTemplate->getTable(), [
			'hostid'		=> $hostId,
			'template_id'	=> $this->templateId_1
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $hostId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_RESOURCE,
				'action'		=> AuditLogAbstract::INSERT_ACTION,
				'description'	=> 'host name',
			]);
		}
	}

	public function testUpdateHost()
	{
		$hostId				= 18;
		$templateProductId	= 16;
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$this->createDependencies();
		Host::factory()->create([
			'hostid'	=> $hostId,
			'user_id'	=> 1
		]);
		HostGroup::factory()->create([
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_1
		]);
		HostInterface::factory()->create([
			'hostid'	=> $hostId,
			'type'		=> HostHelper::AGENT_INTERFAC,
			'main'		=> 1,
			'useip'		=> 1,
			'ip'		=> '192.168.1.1',
			'dns'		=> '',
			'port'		=> 1,
		]);
		
		Product::factory()->create([
			'product_id'	=> $templateProductId,
			'product_type'	=> TEMPLATE,
			'entity_id'		=> $this->templateId_1,
			'product_cat'	=> PERMANENT
		]);
		
		UserInventory::factory()->count(2)->create([
			'user_id'		=> 1,
			'product_id'	=> $templateProductId,
			'product_count'	=> 1,
		]);
		
		
		$this->getMonitoring()->host()->mock([
			'hostids'	=> [$hostId]
		]);

		$this->put(route('host.update', ['hostObject'=>$hostId]),
			[
				'host'			=> 'updated name',
				'interfaces'	=> [
					[
						'type'	=> HostHelper::AGENT_INTERFAC,
						'main'	=> 1,
						'useip'	=> 1,
						'ip'	=> '192.168.3.1',
						'dns'	=> '',
						'port'	=> '10050'
					],
					[
						'type'	=> HostHelper::SNMP_INTERFACE,
						'main'	=> 1,
						'useip'	=> 0,
						'ip'	=> '',
						'dns'	=> 'www.localhost.local',
						'port'	=> '11050'
					],
				],
				'groups'	=> [
					$this->groupId_1,
					$this->groupId_2
				],
				'templates'	=> [
					$this->templateId_1
				],
				'macros'	=> [
					[
						'macro'	=> HostHelper::AUTHENTICATION_PASSWORD,
						'value'	=> '123321'
					],
					[
						'macro'	=> HostHelper::PRIVACY_PASSWORD,
						'value'	=> '456'
					],
				],
			],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$host			= new Host();
		$hostInterface	= new HostInterface();
		$hostGroup		= new HostGroup();
		$hostMacro		= new HostMacro();
		$hostTemplate	= new HostTemplate();
		$this->assertDatabaseHas($host->getTable(), [
			'hostid'		=> $hostId,
			'user_id'		=> 1,
			'host'			=> 'updated name',
		]);
		$this->assertDatabaseHas($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> 1,
			'main'		=> 1,
			'useip'		=> 1,
			'ip'		=> '192.168.3.1',
			'port'		=> '10050'
		]);
		$this->assertDatabaseHas($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> HostHelper::SNMP_INTERFACE,
			'main'		=> 1,
			'useip'		=> 0,
			'dns'		=> 'www.localhost.local',
			'port'		=> '11050'
		]);
		$this->assertDatabaseHas($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_1
		]);
		$this->assertDatabaseHas($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_2
		]);
		$this->assertDatabaseHas($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::AUTHENTICATION_PASSWORD,
			'macro_value'	=> '123321'
		]);
		$this->assertDatabaseHas($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::PRIVACY_PASSWORD,
			'macro_value'	=> '456'
		]);
		$this->assertDatabaseHas($hostTemplate->getTable(), [
			'hostid'		=> $hostId,
			'template_id'	=> $this->templateId_1
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $hostId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> 'updated name',
			]);
		}
	}
	
	public function testDeleteHost()
	{
		$hostId	= 18;
		
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$this->createDependencies();
		Host::factory()->create([
			'hostid'	=> $hostId,
			'user_id'	=> 1
		]);
		
		$this->getMonitoring()->host()->mock([
			'hostids'	=> [$hostId]
		]);
		
		$this->delete(route('host.delete', ['hostObject' => $hostId]), [],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$host			= new Host();
		$hostInterface	= new HostInterface();
		$hostGroup		= new HostGroup();
		$hostMacro		= new HostMacro();
		$hostTemplate	= new HostTemplate();
		$this->assertDatabaseMissing($host->getTable(), [
			'hostid'		=> $hostId,
			'host'			=> 'host name',
		]);
		$this->assertDatabaseMissing($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> 1,
			'main'		=> 1,
			'useip'		=> 1,
			'ip'		=> '192.168.3.1',
			'port'		=> '10050'
		]);
		$this->assertDatabaseMissing($hostInterface->getTable(), [
			'hostid'	=> $hostId,
			'type'		=> HostHelper::SNMP_INTERFACE,
			'main'		=> 1,
			'useip'		=> 0,
			'dns'		=> 'www.localhost.local',
			'port'		=> '11050'
		]);
		$this->assertDatabaseMissing($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_1
		]);
		$this->assertDatabaseMissing($hostGroup->getTable(), [
			'hostid'	=> $hostId,
			'group_id'	=> $this->groupId_2
		]);
		$this->assertDatabaseMissing($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::AUTHENTICATION_PASSWORD,
			'macro_value'	=> '123321'
		]);
		$this->assertDatabaseMissing($hostMacro->getTable(), [
			'hostid'		=> $hostId,
			'macro'			=> HostHelper::PRIVACY_PASSWORD,
			'macro_value'	=> '456'
		]);
		$this->assertDatabaseMissing($hostTemplate->getTable(), [
			'hostid'		=> $hostId,
			'template_id'	=> $this->templateId_1
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $hostId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_RESOURCE,
				'action'		=> AuditLogAbstract::DELETE_ACTION
			]);
		}
	}
}
