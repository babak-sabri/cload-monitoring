<?php
namespace App\Helpers;

use App\Models\Product\Product;
use App\Models\Product\UserInventory;

class InventoryHelper
{
	public static function addUserInventories($userId, array $products)
	{
		$productIds		= [];
		$productsCount	= [];
		foreach ($products as $product) {
			$productIds[]							= $product['product_id'];
			$productsCount[$product['product_id']]	= $product['product_count'];
		}
		if(!empty($productIds)) {
			foreach (Product::select(['product_id', 'product_cat'])->whereIn('product_id', $productIds)->cursor() as $product) {
				$userInventory	= UserInventory::firstOrNew([
					'user_id'		=> $userId,
					'product_id'	=> $product->getKey()
				]);
				$count	= 0;
				if($product->product_cat == PERMANENT) {
					$userInventory->product_count	= 1;
					$count							= 1;
				} else {
					$userInventory->product_count	+= $productsCount[$product->product_id];
				}
				$userInventory->save();
			}
		}
	}
}