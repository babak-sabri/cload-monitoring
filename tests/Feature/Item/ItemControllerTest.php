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

class ItemControllerTest extends TestCase
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
		Host::factory()->create([
			'hostid'	=> $this->hostId_3,
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

	public function testGetItemsList()
	{
		$this->userActingAs([
			'id'		=> $this->userId,
			'user_type'	=> CUSTOMER_USER
		]);
		
		Host::factory()->create([
			'user_id'	=> $this->userId,
			'hostid'	=> $this->hostId_3,
		]);

		$this->createDependencies($this->userId);
		
		$this->getMonitoring()->item()->mock([
			[
				'itemid'		=> '32086',
				'hostid'		=> $this->hostId_3,
				'name'			=> 'eqweqwe',
				'key_'			=> 'net.if.out[if,<mode>]',
				'value_type'	=> '3',
			]
		]);
		
		$this->get(route('item.index', [
				'hostids'	=> [$this->hostId_3],
				'limit'		=> 100
			]),
			[
				'Accept'		=> 'application/json',
			]
		)
		->assertOk()
		->assertJsonStructure([
			'data'	=> [
				'*'	=> [
					'itemid',
					'name',
					'key_',
					'value_type',
					'hostid'
				]
			]
		])
		;
	}
}
