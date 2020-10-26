<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix\API;

use Kayer\Monitoring\APIsInterfaces\TemplateInterface;
use Kayer\Monitoring\APIsAbstracts\TemplateAbstract;
use Kayer\Monitoring\Exceptions\MonitoringException;


class Template extends TemplateAbstract implements TemplateInterface
{
	public function get(array $params = array())
	{
		$params['output']	= [
			'templateid',
			'name'
		];
		$result	= $this->adaptor->call('template.get', $params);
		if(isset($result['result'])) {
			$resultArray	= [];
			foreach ($result['result'] as $value) {
				$resultArray[$value['templateid']]	= $value;
			}
			return $resultArray;
		}
		throw new MonitoringException(config('monitoring.failed-operation'));
	}
}