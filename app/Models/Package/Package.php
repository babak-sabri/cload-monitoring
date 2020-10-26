<?php

namespace App\Models\Package;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Traits\FetchDataTrait;
use App\Helpers\PaginateHelper;

class Package extends BaseModel
{
	use HasFactory, FetchDataTrait;
	
	const ACTIVE_PACKAGE	= 1; 
	const INACTIVE_PACKAGE	= 2; 
	protected $table		= 'packages';
	protected $primaryKey	= 'package_id';
	
	protected $fillable	= [
		'title',
		'description',
		'price',
		'status',
	];
	
	/**
	 * The attributes that can be search.
	 *
	 * @var array
	 */
	protected $searchale	= [
		'package_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'title'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		],
		'description'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		],
		'status'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		]
	];
	
	public function productItems()
	{
		return $this->hasMany(PackageItems::class, 'package_id', 'package_id');
	}
}
