<?php
namespace Kayer\Notification\Email\Kayer;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kayer\Notification\Email\EmailInterface;
use Illuminate\Support\Facades\Mail;

class Email extends Mailable implements EmailInterface
{
	use Queueable, SerializesModels;
	
	public function build()
    {
		
        return $this->from('sender@example.com')
					->markdown('notification::email')
//					->text('mails.demo_plain')
					->with([
						'testVarOne' => '1',
						'testVarTwo' => '2',
					])
					->attach(public_path('/images').'/demo.jpg', [
					'as' => 'demo.jpg',
					'mime' => 'image/jpeg',
					]);
	}
}