<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Models\Users\User;
use Laravel\Passport\Passport;
use App\Helpers\Arr;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Models\AuditLog\AuditLog;
use Kayer\Monitoring\MonitoringInterface;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
	
	public function __construct($name = null, $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
	}
	
	protected function userActingAs(array $userParam=[])
	{
		Passport::actingAs(
			User::factory()->create($userParam)
		);
	}
	protected function assertDataBase($tableName, $data, $missing = false) 
	{
		if(Arr::isAssoc($data)) {
			$data	= [$data];
		}
		foreach ($data as $record) {
			if($missing) {
				$this->assertDatabaseMissing($tableName, $record);
			} else {
				$this->assertDatabaseHas($tableName, $record);
			}
		}
	}
	
	protected function assertAuditLog($params)
	{
		if(config('audit-log.repository')==AuditLogAbstract::MYSQL_LOGGER) {
			$auditLog	= new AuditLog();
			$this->assertDatabaseHas($auditLog->getTable(), $params);
		}
	}
	
	protected function getMonitoring()
	{
		return resolve(MonitoringInterface::class);
	}
}
