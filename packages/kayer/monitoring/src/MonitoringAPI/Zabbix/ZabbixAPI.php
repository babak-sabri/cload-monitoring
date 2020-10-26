<?php
namespace Kayer\Monitoring\MonitoringAPI\Zabbix;

use Kayer\Monitoring\MonitoringInterface;
use Kayer\Monitoring\APIsInterfaces\HostGroupInterface;
use Kayer\Monitoring\APIsInterfaces\TemplateInterface;
use Kayer\Monitoring\APIsInterfaces\HostInterface;
use Kayer\Monitoring\APIsInterfaces\GraphInterface;
use Kayer\Monitoring\APIsInterfaces\ItemInterface;

class ZabbixAPI implements MonitoringInterface
{
	private $hostGroup;
	private $template;
	private $host;
	private $graph;
	private $item;

	public function hostGroup() : HostGroupInterface
	{
		if(is_null($this->hostGroup)) {
			$this->hostGroup	= resolve(HostGroupInterface::class);
		}
		return $this->hostGroup;
	}
	
	public function template() : TemplateInterface
	{
		if(is_null($this->template)) {
			$this->template	= resolve(TemplateInterface::class);
		}
		return $this->template;
	}
	
	public function host() : HostInterface
	{
		if(is_null($this->host)) {
			$this->host	= resolve(HostInterface::class);
		}
		return $this->host;
	}

	public function graph() : GraphInterface
	{
		if(is_null($this->graph)) {
			$this->graph	= resolve(GraphInterface::class);
		}
		return $this->graph;
	}

	public function item(): ItemInterface
	{
		if(is_null($this->item)) {
			$this->item	= resolve(ItemInterface::class);
		}
		return $this->item;
	}

}