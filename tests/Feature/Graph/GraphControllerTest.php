<?php
namespace Tests\Feature\Graph;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\HostGroup\HostGroup as HostGroupModel;
use App\Helpers\HostHelper;
use App\Models\Host\Host;
use App\Models\Host\HostGroup;
use App\Models\Graph\GraphCache;
use App\Models\Host\HostInterface;
use App\Models\Host\HostMacro;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\AuditLog\AuditLog;
use App\Models\Host\HostTemplate;
use App\Helpers\PaginateHelper;
use App\Models\Product\Product;
use App\Models\Product\UserInventory;

class GraphControllerTest extends TestCase
{
	use RefreshDatabase;
	private $groupId_1;
	private $hostId_1;
	private $hostId_2;
	private $hostId_3;
	private $userId	= 6;

	private function createDependencies($userId=1)
	{
		$this->groupId_1	= env('ZABBIX_TESSTING_GROUP_ID_1', 1);
		$this->hostId_1		= env('ZABBIX_TESSTING_HOST_ID_1', 10322);
		$this->hostId_2		= env('ZABBIX_TESSTING_HOST_ID_2', 10323);
		$this->hostId_3		= env('ZABBIX_TESSTING_HOST_ID_3', 10332);
		
		//Create host groups for test
		HostGroupModel::factory()->create([
			'group_id'	=> $this->groupId_1,
			'user_id'	=> $userId,
		]);
		
		Host::factory()->create([
			'hostid'	=> $this->hostId_1,
			'user_id'	=> $userId
		]);
		Host::factory()->create([
			'hostid'	=> $this->hostId_2,
			'user_id'	=> $userId
		]);
		HostGroup::factory()->create([
			'hostid'	=> $this->hostId_1,
			'group_id'	=> $this->groupId_1
		]);
		HostGroup::factory()->create([
			'hostid'	=> $this->hostId_2,
			'group_id'	=> $this->groupId_1
		]);
	}

	public function testSyncUserGraphs()
	{
		$this->userActingAs([
			'id'		=> $this->userId,
			'user_type'	=> CUSTOMER_USER
		]);
		Host::factory()->create([
			'user_id'	=> $this->userId
		]);

		$this->createDependencies($this->userId);
		
		$this->getMonitoring()->graph()->mock([
			[
				'graphid'	=> '10',
				'name'		=> 'graph name 1',
				'hosts'		=> [
					[
						'hostid'	=> $this->hostId_1
					]
				],
				'templates'	=> [
					[
						'templateid'	=> 123,
						'name'			=> 'test template name 1'
					]
				]
			],
			[
				'graphid'	=> '11',
				'name'		=> 'graph name 2',
				'hosts'		=> [
					[
						'hostid'	=> $this->hostId_1
					]
				],
				'templates'	=> [
					[
						'templateid'	=> 456,
						'name'			=> 'test template name 2'
					]
				]
			],
			[
				'graphid'	=> '12',
				'name'		=> 'graph name 3',
				'hosts'		=> [
					[
						'hostid'	=> $this->hostId_2
					]
				],
				'templates'	=> [
					[
						'templateid'	=> 789,
						'name'			=> 'test template name 3'
					]
				]
			],
		]);

		$this->post(route('graph.sync'), [],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		;
		$GraphCache	= new GraphCache();
		$this->assertDatabaseHas($GraphCache->getTable(), [
			'graphid'		=> '10',
			'graph_name'	=> 'graph name 1',
			'hostid'		=> $this->hostId_1,
			'user_id'		=> $this->userId,
			'templateid'	=> 123,
			'template_name'	=> 'test template name 1'
		]);
		$this->assertDatabaseHas($GraphCache->getTable(), [
			'graphid'		=> '11',
			'graph_name'	=> 'graph name 2',
			'hostid'		=> $this->hostId_1,
			'user_id'		=> $this->userId,
			'templateid'	=> 456,
			'template_name'	=> 'test template name 2'
		]);
		$this->assertDatabaseHas($GraphCache->getTable(), [
			'graphid'		=> '12',
			'graph_name'	=> 'graph name 3',
			'hostid'		=> $this->hostId_2,
			'user_id'		=> $this->userId,
			'templateid'	=> 789,
			'template_name'	=> 'test template name 3'
		]);
	}
	
	public function testSyncUserEmptyGraphs()
	{
		$this->userActingAs([
			'id'		=> $this->userId,
			'user_type'	=> CUSTOMER_USER
		]);
		Host::factory()->create([
			'user_id'	=> $this->userId
		]);

		$this->createDependencies(18);

		$this->post(route('graph.sync'), [],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		;
		$GraphCache	= new GraphCache();
		$this->assertDatabaseMissing($GraphCache->getTable(), [
			'user_id'	=> 18
		]);
	}
	
	public function testStoreGraph()
	{
		$this->userActingAs([
			'id'		=> $this->userId,
			'user_type'	=> CUSTOMER_USER
		]);

		$this->createDependencies(18);

		Host::factory()->create([
			'user_id'	=> $this->userId,
			'hostid'	=> $this->hostId_3,
		]);
		
		Host::factory()->create([
			'user_id'	=> $this->userId,
			'hostid'	=> 10084,
		]);
		
		$this->post(route('graph.store'), [
				'name'	=> 'my graph name',
				'gitems'	=> [
					[
						'itemid'	=> '32086',
						'color'		=> '00AA00'
					],
					[
						'itemid'	=> '29191',
						'color'		=> '00AA00'
					]
				]
			],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_CREATED)
		->assertJsonStructure([
			'data'	=> [
				'id'
			]
		])
		;
	}
}
