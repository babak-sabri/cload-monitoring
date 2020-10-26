<?php
namespace Kayer\Notification\SMS\Kayer;

use Kayer\Notification\SMS\SMSInterface;

class SMS implements SMSInterface
{
	/**
	 * send sms notification
	 * 
	 * @param array|string $receptors array of receptors number
	 * @param array|string $message array of messages
	 */
	public function send($receptors, $message)
	{
		//@TODO implement of sending sms
		return true;
	}
}