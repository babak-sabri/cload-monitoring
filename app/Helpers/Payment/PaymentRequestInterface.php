<?php
namespace App\Helpers\Payment;

use App\Models\Invoice\Invoice;

interface PaymentRequestInterface
{
	public function rules();
	
	public function verify($params);
	
	/**
	 * mark invoice as payed
	 * @param type $params
	 */
	public function update($params, Invoice $invoice);
}