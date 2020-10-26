<?php
namespace App\Models\Product;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;
use App\Helpers\PaginateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends BaseModel
{
	use FetchDataTrait, HasFactory;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table	= 'products';
	
	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey	= 'product_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable	= [
		'title',
		'description',
		'price',
		'product_type',
		'entity_id',
		'product_cat'
	];
	
	/**
	 * The attributes that can be search.
	 *
	 * @var array
	 */
	protected $searchale	= [
		'product_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'title'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		],
		'description'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		],
		'product_type'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'product_cat'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'entity_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
	];
}
