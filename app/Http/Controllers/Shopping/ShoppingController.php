<?php
namespace App\Http\Controllers\Shopping;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\ControllerAbstract;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLog\AuditLogAbstract;
use App\Helpers\PaginateHelper;
use App\Http\Requests\Shopping\ShoppingRequest;
use App\Http\Requests\Shopping\ShoppingIndexRequest;
use App\Models\Product\Product;
use App\Models\Product\ShoppingCart;
use App\Models\Bank\Bank;
use App\Helpers\InventoryHelper;
use App\Helpers\PaymentHelper;
use App\Models\Bank\PaymentLog;
use App\Models\Package\Package;

class ShoppingController extends ControllerAbstract
{
	public function index(ShoppingIndexRequest $request)
	{
		try {
			$fetchParams			= $request->validated();
			return $this->getResponse(
				PaymentLog::userPayments($request->user()->id)->fetchData($fetchParams)
					->paginate($fetchParams[PaginateHelper::RECORD_COUNT])
				);
		} catch (Exception $e) {
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function store(ShoppingRequest $request)
	{
		DB::beginTransaction();
		try {
			$shoppingItems		= [];
			$shoppingProducts	= [];
			$totalPrice			= 0;
			$data				= $request->validated();
			$bank				= Bank::find($request->user()->id);

			foreach ($data['products'] as $value) {
				$shoppingProducts[$value['product_id']]	= $value;
				$productIds[]							= $value['product_id'];
			}
			$products	= [];
			
			foreach (Product::whereIn('product_id', $productIds)->cursor() as $product) {
				$products[]			= $product;
				$productCount		= $product->product_cat == COUNTABLE ?  $shoppingProducts[$product->getKey()]['product_count'] : 1;
				$totalPrice			+= $productCount * $product->price;
 				$shoppingItems[]	= [
					'product_id'	=> $product->getKey(),
					'product_count'	=> $productCount,
					'price'			=> $product->price,
					'total_price'	=> $productCount * $product->price
				];
			}
			
			if(!isset($bank->amount) || $bank->amount<$totalPrice) {
				DB::rollBack();
				return $this->getResponse([], Response::HTTP_PAYMENT_REQUIRED);
			}
			
			//Save shopping cart
			$shoppingCart	= new ShoppingCart([
				'user_id'		=> $request->user()->id,
				'total_price'	=> $totalPrice,
			]);
			$shoppingCart->save();
			
			//Save shopping cart items
			$shoppingCart->shoppingCartItems()->createMany($shoppingItems);
			
			//decrese user account balance
			PaymentHelper::decreasAccountBalance(
				$request->user()->id,
				$shoppingCart->getKey(),
				$totalPrice,
				config('payment.pay-for.buy-product')
			);
			
			//Add products to user inventories
			InventoryHelper::addUserInventories($request->user()->id, $data['products']);
			
			$this->saveAuditLog(
				$shoppingCart->getKey(),
				AuditLogAbstract::SHOPPIN_CART_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$shoppingCart->toArray()
			);
			
			DB::commit();
			return $this->getResponse([
				'id'	=> $shoppingCart->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
	
	public function buypackage(Package $package)
	{
		DB::beginTransaction();
		try {
			$request	= request();
			$bank		= Bank::find($request->user()->id);
			$package->load('productItems');
			
			if(!isset($bank->amount) || $bank->amount<$package->price) {
				DB::rollBack();
				return $this->getResponse([], Response::HTTP_PAYMENT_REQUIRED);
			}

			foreach ($package->productItems as $product) {
 				$shoppingItems[]	= [
					'product_id'	=> $product->product_id,
					'product_count'	=> $product->count,
					'count'			=> $product->count,
					'price'			=> 0,
					'total_price'	=> 0
				];
			}
			
			//Save shopping cart
			$shoppingCart	= new ShoppingCart([
				'user_id'		=> $request->user()->id,
				'package_id'	=> $package->package_id,
				'total_price'	=> $package->price,
			]);
			$shoppingCart->save();
			
			//Save shopping cart items
			$shoppingCart->shoppingCartItems()->createMany($shoppingItems);

			//decrese user account balance
			PaymentHelper::decreasAccountBalance(
				$request->user()->id,
				$shoppingCart->getKey(),
				$package->price,
				config('payment.pay-for.buy-package')
			);

			//Add products to user inventories
			InventoryHelper::addUserInventories($request->user()->id, $shoppingItems);
			
			$this->saveAuditLog(
				$shoppingCart->getKey(),
				AuditLogAbstract::SHOPPIN_CART_RESOURCE,
				AuditLogAbstract::INSERT_ACTION,
				$shoppingCart->toArray()
			);
			
			DB::commit();
			return $this->getResponse([
				'id'	=> $shoppingCart->getKey()
			], Response::HTTP_CREATED);
		} catch (Exception $e) {
			DB::rollBack();
			return $this->getResponse([
				self::EXCEPTION_MESSAGE	=> $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}