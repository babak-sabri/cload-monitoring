<?php
namespace Kayer\Verification\Verifier\SMS;


use Illuminate\Database\Eloquent\Model;
use Kayer\Verification\Verifier\VerifierAbstract;
use Kayer\Notification\SMS\SMSInterface;

class Verifier extends VerifierAbstract
{
	private $smsProvider;
	public function __construct(SMSInterface $smsProvider)
	{
		$this->smsProvider	= $smsProvider;
	}
	
	public function sendCode(string $code, Model $model)
	{
		$this->saveCode($code, $model);
		$this->smsProvider->send($model->cellphone, $code);
	}
}