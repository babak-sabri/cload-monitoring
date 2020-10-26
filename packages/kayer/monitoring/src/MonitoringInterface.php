<?php
namespace Kayer\Monitoring;

use Kayer\Monitoring\APIsInterfaces\HostGroupInterface;
use Kayer\Monitoring\APIsInterfaces\HostInterface;
use Kayer\Monitoring\APIsInterfaces\TemplateInterface;
use Kayer\Monitoring\APIsInterfaces\GraphInterface;
use Kayer\Monitoring\APIsInterfaces\ItemInterface;

interface MonitoringInterface
{
	public function hostGroup() : HostGroupInterface;
	
	public function host() : HostInterface;
	
	public function template() : TemplateInterface;
	
	public function graph() : GraphInterface;
	
	public function item() : ItemInterface;
}