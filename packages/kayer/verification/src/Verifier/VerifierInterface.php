<?php
namespace Kayer\Verification\Verifier;

use Illuminate\Database\Eloquent\Model;

interface VerifierInterface
{
	/**
	 * save verification code
	 * @param string $code verification code
	 * @param Model $model model
	 */
	public function saveCode(string $code, Model $model);
	
	/**
	 * send verification code
	 * @param string $code verification code
	 * @param Model $model model
	 */
	public function sendCode(string $code, Model $model);
	
	/**
	 * check model is verified or not
	 * @param Model $model
	 * 
	 * @return int|null the verification time
	 */
	public function isVerified(Model $model);
	
	public function verify($verificationCode, Model $model);
	
	public function updateModelVerification(Model $model);
	
	public function resendCode(string $code, Model $model);
}