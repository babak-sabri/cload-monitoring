<?php
namespace Kayer\Verification\Verifier;

use Illuminate\Database\Eloquent\Model;
use Kayer\Verification\Exceptions\ExpiredCodeException;
use Kayer\Verification\Exceptions\NotFoundException;

abstract class VerifierAbstract implements VerifierInterface
{
	public function saveCode(string $code, Model $model)
	{
		$verifierModel	= resolve('VerifyModel');
		$verifierModel->updateOrCreate(
			[
				'user_id'	=> $model->getKey()
			],
			[
				'verification_code'	=> $code,
				'expiration_date'	=> now()->timestamp + config('verification.verification-expiration-seconds')
			]
		);
	}
	
	public function verify($verificationCode, Model $model)
	{
		$verifierModel	= resolve('VerifyModel');
		$verify			= $verifierModel->where('user_id', $model->getKey())
							->where('verification_code', $verificationCode)
							->first()
							;
		if(empty($verify)) {
			throw new NotFoundException();	
		} else if($verify->expiration_date <= now()->timestamp) {
			throw new ExpiredCodeException();
		}
		
		$this->updateModelVerification($model);
		return true;
	}
	
	public function isVerified(Model $model)
	{
		return $model->cellphone_verified_at;
	}
	
	public function updateModelVerification(Model $model)
	{
		$model->update([
			'cellphone_verified_at'	=> now()->timestamp
		]);
	}
	
	public function resendCode(string $code, Model $model)
	{
		$verifierModel	= resolve('VerifyModel');
		$verify			= $verifierModel
							->where('user_id', $model->getKey())
							->where('expiration_date', '>', now()->timestamp)
							->first()
							;
		
		
		if(empty($verify)) {
			$this->sendCode($code, $model);
		} else if($verify->expiration_date > now()->timestamp) {
			throw new ExpiredCodeException('', $verify->expiration_date - now()->timestamp);
		}
	}
}