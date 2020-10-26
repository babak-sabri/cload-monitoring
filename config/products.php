<?php
return [
	'product_cats'	=> [
		COUNTABLE, PERMANENT
	],
	'products'	=> [
		SMS =>		[
			'product'	=> SMS,
			'type'		=> COUNTABLE
		],
		EMAIL =>	[
			'product'	=> EMAIL,
			'type'		=> COUNTABLE
		],
		ITEM =>		[
			'product'	=> ITEM,
			'type'		=> COUNTABLE
		],
		TEMPLATE =>	[
			'product'	=> TEMPLATE,
			'type'		=> PERMANENT,
		]
	]
];