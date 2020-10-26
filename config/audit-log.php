<?php
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Helpers\AuditLog\Repository\MysqlRepositoryAdapter;

return [
	/*
    |--------------------------------------------------------------------------
    | Audit log resolver
    |--------------------------------------------------------------------------
    |
    |
    */
	'resolver'	=> 'AuditLoger',
	
	/*
    |--------------------------------------------------------------------------
    | Audit logger class
    |--------------------------------------------------------------------------
    |
    |
    */
	'audit-logger'	=> \App\Helpers\AuditLog\AuditLog::class,
	
	/*
    |--------------------------------------------------------------------------
    | Audit log repository
    |--------------------------------------------------------------------------
    |
    |
    */
	'repository'	=> env('AUDIT_LOG_REPOSITORY', AuditLogAbstract::MYSQL_LOGGER),
	
	/*
    |--------------------------------------------------------------------------
    | Audit log mysql handler
    |--------------------------------------------------------------------------
    |
    |
    */
	'mysql-handler'	=> MysqlRepositoryAdapter::class,
];