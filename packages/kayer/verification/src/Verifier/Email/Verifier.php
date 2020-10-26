<?php
namespace Kayer\Verification\Verifier\Email;

use Illuminate\Support\Facades\Mail;
use Kayer\Notification\Email\EmailInterface;
use Illuminate\Database\Eloquent\Model;
use Kayer\Verification\Verifier\VerifierAbstract;

class Verifier extends VerifierAbstract
{
	private $mailProvider;
	public function __construct(EmailInterface $mailProvider)
	{
		$this->mailProvider	= $mailProvider;
	}
	
	public function sendCode(string $code, Model $model)
	{
		$this->saveCode($code, $model);
		Mail::to($model)->send($this->mailProvider);
//		Mail::to($request->user())->send(new OrderShipped($order));
//		$this->mailProvider->send($code, $message);
//		dd($model->toArray());
//		Mail::to($model)
//			->send(new OrderShipped($order));
//		
//		
//		
//		dd($code);
//		$this->smsProvider->send($model->cellphone, $code);
	}
}