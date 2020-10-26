<?php
return [
	/*
    |--------------------------------------------------------------------------
    | Monitoring application
    |--------------------------------------------------------------------------
    |
    | This value specifies monitoring remote application
    |
    */
	'monitoring-app'	=> env('MONITORING_APP', 'zabbix'),
	
	/*
    |--------------------------------------------------------------------------
    | Monitoring cache
    |--------------------------------------------------------------------------
    |
    | This value specifies monitoring cache interface
    |
    */
	'monitoring-cache'	=> env('MONITORING_CACHE', 'file'),
	
	/*
    |--------------------------------------------------------------------------
    | Zabbix failed login message
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix failed login message
    |
    */
	'failed-login-message'	=> 'failed login to remove monitoring software',
	
	/*
    |--------------------------------------------------------------------------
    | Zabbix failed login message
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix failed login message
    |
    */
	'request-droped'	=> 'request droped',
	
	/*
    |--------------------------------------------------------------------------
    | Zabbix failed login message
    |--------------------------------------------------------------------------
    |
    | This value specifies the zabbix failed login message
    |
    */
	'failed-operation'	=> 'monitoring failed opeartion',
	/*
    |--------------------------------------------------------------------------
    | Zabbix failed create graph error
    |--------------------------------------------------------------------------
    |
    | This value specifies error message
    |
    */
	'create-graph-error'	=> 'graph creation error',
];