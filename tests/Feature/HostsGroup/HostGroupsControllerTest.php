<?php
namespace Tests\Feature\HostsGroup;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Models\HostGroup\HostGroup;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\AuditLog\AuditLog;
use Kayer\Monitoring\MonitoringInterface;


class HostGroupsControllerTest extends TestCase
{
	use RefreshDatabase;
	
	/**
	 * store new root node
	 *
	 * @return void
	 */
	public function testStoreRootNode()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$groupId	= 18;
		$monitoring	= resolve(MonitoringInterface::class);
		$monitoring->hostGroup()->mock([
			'groupids'	=> [$groupId]
		]);
		$this->post(route('hostgroup.store'),
			[
				'group_name'	=> 'testname',
				'decription'	=> 'testdescription',
				'parent_id'		=> 0
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
		->assertJson([
			'data' => [
				'id'	=> $groupId
			]
		])
		->getContent()
		;

		$this->assertDatabaseHas('hosts_groups', [
			'group_name'	=> 'testname',
			'parent_id'		=> null
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $groupId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_GROUP_RESOURCE,
				'action'		=> AuditLogAbstract::INSERT_ACTION,
				'description'	=> 'testname',
			]);
		}
	}
	
	/**
	 * store sub group node
	 *
	 * @return void
	 */
	public function testSubGroupNode()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$parentGroupId	= 500;
		$groupId		= 18;
		HostGroup::factory()->create([
			'group_id'	=> $parentGroupId,
			'user_id'	=> 1,
			'_lft'		=> 1,
			'_rgt'		=> 2,
			'parent_id'	=> 0,
		]);
		
		$monitoring	= resolve(MonitoringInterface::class);
		$monitoring->hostGroup()->mock([
			'groupids'	=> [$groupId]
		]);
		$this->post(route('hostgroup.store'),
			[
				'group_name'	=> 'testname',
				'decription'	=> 'testdescription',
				'parent_id'		=> $parentGroupId
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
		->assertJson([
			'data' => [
				'id'	=> $groupId
			]
		])
		->getContent()
		;
		
		$this->assertDatabaseHas('hosts_groups', [
			'group_id'		=> $groupId,
			'group_name'	=> 'testname',
			'_lft'			=> 2,
			'_rgt'			=> 3,
			'parent_id'		=> $parentGroupId,
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $groupId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_GROUP_RESOURCE,
				'action'		=> AuditLogAbstract::INSERT_ACTION,
				'description'	=> 'testname',
			]);
		}
	}
	
	/**
	 * store forbidden sub group
	 *
	 * @return void
	 */
	public function testForbiddenSubGroupNode()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		HostGroup::factory()->create([
			'group_id'	=> 500,
			'user_id'	=> 8,
			'_lft'		=> 1,
			'_rgt'		=> 2,
			'parent_id'	=> 0,
		]);
		
		$this->post(route('hostgroup.store'),
			[
				'group_name'	=> 'testname',
				'decription'	=> 'testdescription',
				'parent_id'		=> 500
			],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertStatus(Response::HTTP_FORBIDDEN)
		;
	}
	
	/**
	 * update a host group
	 *
	 * @return void
	 */
	public function testUpdateHostGroup()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);

		$groupId	= 18;
		$monitoring	= resolve(MonitoringInterface::class);
		$monitoring->hostGroup()->mock([
			'groupids'	=> [$groupId]
		]);
		
		HostGroup::factory()->create([
			'group_id'			=> $groupId,
			'user_id'			=> 1,
			'_lft'				=> 1,
			'_rgt'				=> 2,
			'parent_id'			=> 0,
		]);
		
		$this->put(route('hostgroup.update', ['group' => $groupId]),
			[
				'group_name'	=> 'updated_test_name',
				'decription'	=> 'testdescription'
			],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$this->assertDatabaseHas('hosts_groups', [
			'group_id'		=> $groupId,
			'group_name'	=> 'updated_test_name',
			'decription'	=> 'testdescription',
			'user_id'		=> 1,
			'_lft'			=> 1,
			'_rgt'			=> 2,
			'parent_id'		=> null,
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> $groupId,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_GROUP_RESOURCE,
				'action'		=> AuditLogAbstract::UPDATE_ACTION,
				'description'	=> 'updated_test_name',
			]);
		}
	}
	
	/**
	 * delete a host group
	 *
	 * @return void
	 */
	public function testDeleteHostGroup()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		$groupId	= 15;
		HostGroup::factory()->create([
			'group_id'	=> $groupId,
			'user_id'	=> 1,
			'_lft'		=> 1,
			'_rgt'		=> 6,
			'parent_id'	=> 0,
		]);
		
		HostGroup::factory()->create([
			'group_id'	=> 57,
			'user_id'	=> 1,
			'_lft'		=> 2,
			'_rgt'		=> 5,
			'parent_id'	=> $groupId,
		]);
		
		HostGroup::factory()->create([
			'group_id'	=> 58,
			'user_id'	=> 1,
			'_lft'		=> 3,
			'_rgt'		=> 4,
			'parent_id'	=> 57,
		]);
		
		$monitoring	= resolve(MonitoringInterface::class);
		$monitoring->hostGroup()->mock([
			'groupids'	=> [57, 58]
		]);
		
		$this->delete(route('hostgroup.update', ['group' => 57]),
			[
				'group_name'	=> 'updated_test_name',
				'decription'	=> 'testdescription'
			],
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		;
		
		$this->assertDatabaseHas('hosts_groups', [
			'group_id'	=> 	$groupId,	
			'_lft'		=> 1,
			'_rgt'		=> 2
		]);
		
		$this->assertDatabaseMissing('hosts_groups', [
			'group_id'	=> 41,
		]);

		$this->assertDatabaseMissing('hosts_groups', [
			'group_id'	=> 42,
		]);
		
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> 57,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_GROUP_RESOURCE,
				'action'		=> AuditLogAbstract::DELETE_ACTION,
			]);
			$this->assertDatabaseHas($auditLog->getTable(), [
				'entity_id'		=> 58,
				'user_id'		=> 1,
				'resource'		=> AuditLogAbstract::HOST_GROUP_RESOURCE,
				'action'		=> AuditLogAbstract::DELETE_ACTION,
			]);
		}
	}
	
	public function testHostGroupList()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		HostGroup::factory()->create([
			'user_id'	=> 1,
		]);
		
		$this->get(route('hostgroup.index'),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data'	=> [
				'*'	=> [
					'group_id',
					'group_name',
					'decription',
					'user_id',
				]
			]
		])
		;
	}
	
	public function testHostGroupTreeList()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER,
			'id'	=> 1,
		]);
		HostGroup::factory()->create([
			'group_id'	=> 1,
			'user_id'	=> 1,
			'_lft'		=> 1,
			'_rgt'		=> 4,
			'parent_id'	=> 0,
		]);
		
		HostGroup::factory()->create([
			'group_id'	=> 2,
			'user_id'	=> 1,
			'_lft'		=> 2,
			'_rgt'		=> 3,
			'parent_id'	=> 1,
		]);
		
		$this->get(route('hostgroup.tree'),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data'	=> [
				'*'	=> [
					'group_id',
					'group_name',
					'decription',
					'user_id',
					'children'	=> [
						'*'	=> [
							'group_id',
							'group_name',
							'decription',
							'user_id',
							'children'
						]
					],
				]
			]
		])
		;
	}
	
	public function testShowHostGroup()
	{
		$this->userActingAs([
			'user_type'	=> CUSTOMER_USER
		]);
		HostGroup::factory()->create([
			'user_id'	=> 1,
			'group_id'	=> 1
		]);
		$this->get(route('hostgroup.show', ['group' => 1]),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data'	=> [
					'group_id',
					'group_name',
					'decription',
					'user_id',
			]
		])
		;
	}
	
}