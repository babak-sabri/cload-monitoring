<?php
namespace App\Helpers;

use App\Helpers\Payment\PaymentRequestInterface;
use App\Models\Invoice\Invoice;
use App\Exceptions\PaymentVerificationException;
use App\Models\Bank\Bank;
use App\Models\Bank\PaymentLog;

class PaymentHelper
{
	public static function pay(PaymentRequestInterface $paymentObject, Invoice $invoice, array $paymentParams)
	{
		if(!$paymentObject->verify($paymentParams)) {
			throw new PaymentVerificationException();
		}
		$paymentObject->update($paymentParams, $invoice);
		$bank	= Bank::find($invoice->user_id);
		if(empty($bank)) {
			$bank			= new Bank();
			$bank->user_id	= $invoice->user_id;
			$bank->amount	= $invoice->amount;
		} else {
			$bank->amount	+= $invoice->amount;
		}
		$bank->save();
	}
	
	public static function decreasAccountBalance($userId, $entityId, $price, $payFor)
	{
		$bank		= Bank::find($userId);
		$paymentLog	= new PaymentLog([
			'user_id'	=> $userId,
			'price'		=> $price,
			'entity_id'	=> $entityId,
			'pay_for'	=> $payFor
		]);
		$bank->amount	-= $price;
		$bank->save();
		$paymentLog->save();
	}
}