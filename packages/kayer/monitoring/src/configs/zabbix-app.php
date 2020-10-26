<?php
return [
	'provider'	=> [
		'interface'	=> Kayer\Monitoring\MonitoringInterface::class,
		'class'		=> Kayer\Monitoring\MonitoringAPI\Zabbix\ZabbixAPI::class
	],
	'adaptor'	=> [
		'interface'	=> Kayer\Monitoring\AdaptorInterface::class,
		'class'		=> Kayer\Monitoring\MonitoringAPI\Zabbix\Adaptor::class
	],
	/*
    |--------------------------------------------------------------------------
    | Zabbix API URL
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix API url
    |
    */
	'url'		=> env('ZABBIX_API_URL', 'http://www.zabbix.local/api_jsonrpc.php'),
	/*
    |--------------------------------------------------------------------------
    | Zabbix Username
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix username
    |
    */
	'username'	=> env('ZABBIX_USERNAME', 'admin'),
	/*
    |--------------------------------------------------------------------------
    | Zabbix Password
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix Password
    |
    */
	'password'	=> env('ZABBIX_PASSWORD', 'zabbix'),
	
	/*
    |--------------------------------------------------------------------------
    | Zabbix APIs interfaces
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix api interfaces
    |
    */
	'api-list'	=> [
		[
			'interface'	=> Kayer\Monitoring\APIsInterfaces\HostGroupInterface::class,
			'class'		=> Kayer\Monitoring\MonitoringAPI\Zabbix\API\HostGroup::class
		],
		[
			'interface'	=> Kayer\Monitoring\APIsInterfaces\TemplateInterface::class,
			'class'		=> \Kayer\Monitoring\MonitoringAPI\Zabbix\API\Template::class
		],
		[
			'interface'	=> \Kayer\Monitoring\APIsInterfaces\HostInterface::class,
			'class'		=> Kayer\Monitoring\MonitoringAPI\Zabbix\API\Host::class
		],
		[
			'interface'	=> \Kayer\Monitoring\APIsInterfaces\GraphInterface::class,
			'class'		=> Kayer\Monitoring\MonitoringAPI\Zabbix\API\Graph::class
		],
		[
			'interface'	=> \Kayer\Monitoring\APIsInterfaces\ItemInterface::class,
			'class'		=> Kayer\Monitoring\MonitoringAPI\Zabbix\API\Item::class
		]
	]
];