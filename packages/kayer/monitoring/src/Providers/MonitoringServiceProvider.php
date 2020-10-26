<?php
namespace Kayer\Monitoring\Providers;

use Illuminate\Support\ServiceProvider;

class MonitoringServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
		if(config('monitoring.monitoring-app', false)) {
			$monitoringApp	= config('monitoring.monitoring-app');

			// Register monitoring application
			$monitoringApi	= config("{$monitoringApp}-app.provider");
			$this->app->bind(
				$monitoringApi['interface'],
				$monitoringApi['class']
			);

			// Register monitoring connection adaptor
			$monitoringAdaptor	= config("{$monitoringApp}-app.adaptor");
			$this->app->singleton(
				$monitoringAdaptor['interface'],
				$monitoringAdaptor['class']
			);

			// Register monitoring url
			$this->app->when($monitoringAdaptor['class'])
				->needs('$url')
				->give(config("{$monitoringApp}-app.url"));

			// Register monitoring username
			$this->app->when($monitoringAdaptor['class'])
				->needs('$username')
				->give(config("{$monitoringApp}-app.username"));

			// Register monitoring password
			$this->app->when($monitoringAdaptor['class'])
				->needs('$password')
				->give(config("{$monitoringApp}-app.password"));

			// Bind APIs
			$apiLis	= config("{$monitoringApp}-app.api-list", []);
			foreach($apiLis as $apiInterface)
			{
				$this->app->bind(
					$apiInterface['interface'],
					$apiInterface['class']
				);
			}
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
				__DIR__.'/../configs/monitoring.php' => config_path('monitoring.php'),
				__DIR__.'/../configs/zabbix-app.php' => config_path('zabbix-app.php'),
			]);
		}
	}
}
