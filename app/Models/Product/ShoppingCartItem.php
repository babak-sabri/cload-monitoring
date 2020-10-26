<?php
namespace App\Models\Product;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;

class ShoppingCartItem extends BaseModel
{
	use FetchDataTrait;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table	= 'shopping_cart_items';
	
	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey	= 'shopping_cart_item_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable	= [
		'product_id',
		'product_count',
		'price',
		'total_price',
	];
	
	public $timestamps	= false;

}

