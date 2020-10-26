<?php
namespace App\Models\Product;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;
use App\Helpers\PaginateHelper;
use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserInventory extends BaseModel
{
	use FetchDataTrait, HasFactory;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table	= 'user_inventories';
	
	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey	= 'inventory_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable	= [
		'user_id',
		'product_id',
		'product_count',
	];
	
	/**
	 * The attributes that can be search.
	 *
	 * @var array
	 */
	protected $searchale	= [
		'inventory_id'	=> [
			'type'	=> PaginateHelper::SCALAR_TYPE
		],
		'product_id'	=> [
			'type'	=> PaginateHelper::LIKE_STRING_TYPE
		],
		'productType'	=> [
			'type'	=> PaginateHelper::CLOSURE_TYPE
		]
	];
	
	public function scopeProductType($query, $params)
	{
		$query->whereIn('user_inventories.product_id', function($query) use($params){
			$query->select('product_id')
				->from(with(new Product)->getTable())
				->whereIn('product_type', $params)
			;
		});
		return $query;
	}
	
	public function scopeUserInventories($query, $userId)
	{
		$query->where('user_inventories.user_id', $userId);
		return $query;
	}
}