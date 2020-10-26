<?php
namespace App\Helpers\Payment\Requests;

use App\Helpers\Payment\PaymentRequestInterface;
use App\Base\BaseRequest;
use App\Models\Invoice\Invoice;
use Carbon\Carbon;

class ManualRequest extends BaseRequest implements PaymentRequestInterface
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return is_null(request()->invoice->payed_at);
	}
	
	public function rules()
	{
		return [
			'tracking_code'	=> 'required|string|max:255'
		];
	}

	public function verify($params)
	{
		return true;
	}

	public function update($params, Invoice $invoice)
	{
		$invoice->tracking_code	= $params['tracking_code'];
		$invoice->pay_type		= request()->payType;
		$invoice->payed_at		= Carbon::now()->timestamp;
		$invoice->save();
	}
}