<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix\API;

use Kayer\Monitoring\APIsInterfaces\HostInterface;
use Kayer\Monitoring\APIsAbstracts\HostAbstract;
use Kayer\Monitoring\Exceptions\MonitoringException;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class Host extends HostAbstract implements HostInterface
{
	/**
	 * convert data to zabbix format
	 * @param type $params
	 */
	private function prepareData($params)
	{
		$result	= [];
		
		if(isset($params['api_host_name'])) {
			$result['host']	= $params['api_host_name'];
		}
		
		if(isset($params['groups'])) {
			foreach($params['groups'] as $groupId) {
				$result['groups'][]	= [
					'groupid'	=> $groupId
				];
			}
		}
		
		if(isset($params['hostid'])) {
			$result['hostid']	= $params['hostid'];
		}
		
		if(isset($params['interfaces'])) {
			foreach($params['interfaces'] as $interface) {
				foreach($interface as $key=>$i) {
					if(is_null($i)) {
						$interface[$key]	= "";
					}
				}
				$result['interfaces'][]	= $interface;
			}
		}
		
		if(isset($params['templates'])) {
			foreach($params['templates'] as $templateId) {
				$result['templates'][]	= [
					'templateid'	=> $templateId
				];
			}
		}
		
		if(isset($params['macros'])) {
			foreach($params['macros'] as $macro) {
				$result['macros'][]	= [
					'macro'	=> $macro['macro'],
					'value'	=> $macro['value']
				];
			}
		}
		return $result;
	}
	
	private function call($api, $params)
	{
		$result	= $this->adaptor->call($api, $this->prepareData($params));
		$ids	= Arr::get($result, 'result.hostids', false);
		$error	= Arr::get($result, 'error.data', '');

		if($ids) {
			return $ids;
		} else if(Str::contains($error, 'inherited from another template')) {
			return self::DUPLICATED_TEMPLATES_ERROR;
		}
		throw new MonitoringException(config('monitoring.failed-operation'));
	}
	
	public function create(array $params)
	{
		$result = $this->call('host.create', $params);
		return $result[0];
	}

	public function update(int $hostId, array $params)
	{
		$params['hostid']	= $hostId;
		return $this->call('host.update', $params);
	}
	
	public function delete($hostIds)
	{
		$result	= Arr::get($this->adaptor->call('host.delete', Arr::wrap($hostIds)), 'result.hostids', false);
		if($result) {
			return $result;
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