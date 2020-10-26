<?php
use App\Models\Package\Package;
return [
	/*
    |--------------------------------------------------------------------------
    | Application Packages Products Category
    |--------------------------------------------------------------------------
    |
    | This value specifies the application supporded packages products category
    |
    */
	'product-status'	=> [
		Package::ACTIVE_PACKAGE,
		Package::INACTIVE_PACKAGE,
	],
];