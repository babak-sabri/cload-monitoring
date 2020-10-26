<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\AuditLog\Repository\AdapterRepositoryInterface;

class AuditLogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		$this->app->singleton(
			config('audit-log.resolver'),
			config('audit-log.audit-logger')
		);
		
		$this->app->singleton(
			AdapterRepositoryInterface::class,
			config('audit-log.'.config('audit-log.repository').'-handler')
		);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
