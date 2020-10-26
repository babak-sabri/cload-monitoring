<?php
namespace App\Models\Product;

use App\Base\BaseModel;
use App\Models\Traits\FetchDataTrait;

class ShoppingCart extends BaseModel
{
	use FetchDataTrait;
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table	= 'shopping_carts';
	
	/**
	 * The primary key associated with the table.
	 *
	 * @var string
	 */
	protected $primaryKey	= 'shopping_cart_id';
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable	= [
		'user_id',
		'package_id',
		'total_price',
	];
	
	public function shoppingCartItems()
	{
		return $this->hasMany(ShoppingCartItem::class, 'shopping_cart_id', 'shopping_cart_id');
	}
}
