<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix\API;

use Kayer\Monitoring\APIsInterfaces\ItemInterface;
use Kayer\Monitoring\APIsAbstracts\ItemAbstract;
use Kayer\Monitoring\Exceptions\MonitoringException;
use App\Helpers\Arr;
use Illuminate\Support\Facades\Http;

class Item extends ItemAbstract implements ItemInterface
{
	
	public function get(array $params)
	{
		if(!Arr::get($params, 'output', false)) {
			$params['output']	= [
				'itemid',
				'name',
				'key_',
				'value_type',
				'hostid'
			];
		}
		return Arr::get($this->adaptor->call('item.get', $params), 'result', []);
	}

	public function create(array $params) {
		
	}

	public function delete(array $hostIds) {
		
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

	public function update(int $hostId, array $params) {
		
	}
}