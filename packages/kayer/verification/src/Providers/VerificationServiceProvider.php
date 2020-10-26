<?php
namespace Kayer\Verification\Providers;

use Illuminate\Support\ServiceProvider;

class VerificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$verifyMethod	= config('verification.verification-method', false);
		if($verifyMethod) {
			$this->app->bind(
				\Kayer\Verification\Verifier\VerifierInterface::class,
				"Kayer\Verification\Verifier\\{$verifyMethod}\\Verifier"
			);
			$this->app->bind(
				'VerifyModel',
				config('verification.verify-model')
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
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__.'/../configs/verification.php' => config_path('verification.php')
			]);
		}
	}
}
