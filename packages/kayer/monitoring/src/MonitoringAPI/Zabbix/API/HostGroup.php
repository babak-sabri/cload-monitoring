<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix\API;

use Kayer\Monitoring\APIsInterfaces\HostGroupInterface;
use Kayer\Monitoring\APIsAbstracts\HostGroupAbstract;
use Kayer\Monitoring\Exceptions\MonitoringException;
use App\Helpers\Arr;
use Illuminate\Support\Facades\Http;


class HostGroup extends HostGroupAbstract implements HostGroupInterface
{
	public function create(array $params)
	{
		$result	= Arr::get($this->adaptor->call('hostgroup.create', $params), 'result.groupids.0', null);
		if(!is_null($result)) {
			return $result;
		}
		throw new MonitoringException(config('monitoring.failed-operation'));
	}

	public function update(int $hostGroupId, array $params)
	{
		$params['groupid']	= $hostGroupId;
		$result				= Arr::get($this->adaptor->call('hostgroup.update', $params), 'result.groupids.0', null);
		if(!is_null($result)) {
			return $result;
		}
		throw new MonitoringException(config('monitoring.failed-operation'));
	}
	
	public function delete(array $hostGroupIds)
	{
		$result	= $this->adaptor->call('hostgroup.delete', $hostGroupIds);
		if(empty(Arr::arrayDiff($hostGroupIds, Arr::get($result, 'result.groupids', [])))) {
			return true;
		}
		throw new MonitoringException(config('monitoring.failed-operation'));
	}

	public function mock(array $result = [])
	{
		Http::fake([
			$this->adaptor->getUrl() => Http::response([
				'jsonrpc'	=> '2.0',
				'result'	=> $result,
				'id'		=> 1
			], 200)
		]);
	}
}