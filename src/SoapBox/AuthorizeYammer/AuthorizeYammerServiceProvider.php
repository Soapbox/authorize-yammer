<?php namespace SoapBox\AuthorizeYammer;

use Illuminate\Support\ServiceProvider;

class AuthorizeYammerServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('soapbox/authorize-yammer');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		StrategyFactory::register('yammer', 'SoapBox\AuthorizeLinkedin\YammerStrategy');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}