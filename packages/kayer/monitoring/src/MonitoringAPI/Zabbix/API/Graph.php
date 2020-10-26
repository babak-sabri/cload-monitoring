<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix\API;

use Kayer\Monitoring\APIsInterfaces\GraphInterface;
use Kayer\Monitoring\APIsAbstracts\GraphAbstract;
use Kayer\Monitoring\Exceptions\MonitoringException;
use App\Helpers\Arr;
use Illuminate\Support\Facades\Http;

class Graph extends GraphAbstract implements GraphInterface
{
	
	public function get(array $params)
	{
		$graphs	= [];
		if(!Arr::get($params, 'selectHosts', false)) {
			$params['selectHosts']	= ['hostid'];
		}
		if(!Arr::get($params, 'output', false)) {
			$params['output']	= ['name'];
		}
		
		$result = Arr::get($this->adaptor->call('graph.get', $params), 'result', []);

		foreach ($result as $graph) {
			$graphs[]	= [
				'graphid'				=> $graph['graphid'],
				'graph_name'			=> $graph['name'],
				'hostid'				=> Arr::get($graph, 'hosts.0.hostid', 0),
				'templateid'			=> Arr::get($graph, 'templates.0.templateid', 0),
				'template_name'			=> Arr::get($graph, 'templates.0.name', 0),
			];
		}
		return $graphs;
	}

	public function create(array $params)
	{
		dd($this->adaptor->call('graph.create', $params));
		$graphId = Arr::get($this->adaptor->call('graph.create', $params), 'result.graphids.0', false);
		if($graphId) {
			return $graphId;
		}
		throw new MonitoringException(config('monitoring.create-graph-error'));
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