<?php

namespace App\Models\Package;

use App\Base\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PackageItems extends BaseModel
{
	use HasFactory;
	
	protected $table		= 'package_items';
	
	protected $primaryKey	= 'package_id';
	
	protected $fillable	= [
		'product_id',
		'count',
	];
	
	public $timestamps	= false;
	
}


