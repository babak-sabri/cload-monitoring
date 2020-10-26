<?php
namespace Kayer\Notification\Providers;

use Illuminate\Support\ServiceProvider;
use Kayer\Notification\SMS\SMSInterface;
use Kayer\Notification\Email\EmailInterface;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$smsProvider	= config('kayer-notification.sms-provider', false);
		if($smsProvider) {
			$this->app->bind(
				SMSInterface::class,
				"Kayer\Notification\SMS\\{$smsProvider}\\SMS"
			);
		}
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
		$this->loadViewsFrom(__DIR__.'/../../resources/views', 'notification');
		
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../configs/kayer-notification.php' => config_path('kayer-notification.php')
			]);
		}
	}
}
