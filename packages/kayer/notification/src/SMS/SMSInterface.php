<?php
namespace Kayer\Notification\SMS;

interface SMSInterface
{
	/**
	 * send sms notification
	 * 
	 * @param array|string $receptors array of receptors number
	 * @param array|string $message array of messages
	 */
	public function send($receptors, $message);
}