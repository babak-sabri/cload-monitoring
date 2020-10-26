<?php

return [
	'Profile'	=> [
		'title'		=> 'User profile',
		'actions'	=> [
			'index'				=> ['title'	=> 'Get user profile'],
			'show'				=> ['ref'	=> 'index'],
			'update'			=> ['title'	=> 'Update profile'],
			'changePassword'	=> ['title'	=> 'change password'],
		],
	],
	'HostGroup'	=> [
		'title'		=> 'Customer host group',
		'actions'	=> [
			'store'		=> ['title'	=> 'store new host group'],
			'update'	=> ['title'	=> 'update a host group'],
			'delete'	=> ['title'	=> 'delete a host group'],
			'index'		=> ['title'	=> 'Get user host groups'],
			'show'		=> ['ref'	=> 'index'],
			'tree'		=> ['ref'	=> 'index'],
		],
	],
	'Host'	=> [
		'title'		=> 'Customer hosts',
		'actions'	=> [
			'store'		=> ['title'	=> 'store a new host'],
			'update'	=> ['title'	=> 'update a host'],
			'delete'	=> ['title'	=> 'delete a host'],
			'index'		=> ['title'	=> 'Get user host'],
			'show'		=> ['ref'	=> 'index'],
		],
	],
	'Invoice'	=> [
		'title'		=> 'Customer invoices',
		'actions'	=> [
			'store'		=> ['title'	=> 'store an invoice'],
			'update'	=> ['title'	=> 'update an invoice'],
			'delete'	=> ['title'	=> 'delete  an invoice'],
			'index'		=> ['title'	=> 'Get user invoices'],
			'show'		=> ['ref'	=> 'index'],
			'pay'		=> ['title'	=> 'pay invoice'],
		],
	],
	'Shopping'	=> [
		'title'		=> 'Customer shopping',
		'actions'	=> [
			'index'		=> ['title'	=> 'shoppings list'],
			'store'		=> ['title'	=> 'store a shopping cart'],
			'buypackage'=> ['title'	=> 'buy a package'],
		],
	],
	'Product'	=> [
		'title'		=> 'Product',
		'actions'	=> [
			'index'		=> ['title'	=> 'product list'],
			'show'		=> ['ref'	=> 'index'],
			'store'		=> ['title'	=> 'create a product'],
			'update'	=> ['title'	=> 'update a product'],
			'destroy'	=> ['title'	=> 'delete product'],
		],
	],
	'Inventory'	=> [
		'title'		=> 'Inventories',
		'actions'	=> [
			'index'		=> ['title'	=> 'product list']
		],
	],
	'Folk'	=> [
		'title'		=> 'Folk',
		'actions'	=> [
			'index'		=> ['title'	=> 'folks list'],
			'store'		=> ['title'	=> 'store folk'],
		],
	],
	'Graph'	=> [
		'title'		=> 'Graph',
		'actions'	=> [
			'sync'		=> ['title'	=> 'sync user graph'],
			'index'		=> ['title'	=> 'graph list'],
			'store'		=> ['title'	=> 'store graph']
		],
	],
	'Item'	=> [
		'title'		=> 'Item',
		'actions'	=> [
			'index'		=> ['title'	=> 'item list']
		],
	],
	'Template'	=> [
		'title'		=> 'Template products',
		'actions'	=> [
			'index'		=> ['title'	=> 'template list'],
			'show'		=> ['ref'	=> 'index'],
			'store'		=> ['title'	=> 'create new template'],
			'update'	=> ['title'	=> 'update a template'],
			'destroy'	=> ['title'	=> 'delete template(s)'],
		],
	],
	'Currency'	=> [
		'title'		=> 'Currency',
		'actions'	=> [
			'index'		=> ['title'	=> 'currency list'],
			'show'		=> ['ref'	=> 'index'],
			'store'		=> ['title'	=> 'create new currency'],
			'update'	=> ['title'	=> 'update a currency'],
			'destroy'	=> ['title'	=> 'delete currency(s)'],
		],
	],
	'Package'	=> [
		'title'		=> 'Package',
		'actions'	=> [
			'index'		=> ['title'	=> 'package list'],
			'show'		=> ['ref'	=> 'index'],
			'store'		=> ['title'	=> 'create new package'],
			'update'	=> ['title'	=> 'update a package'],
			'destroy'	=> ['title'	=> 'delete package(s)'],
		],
	],
];