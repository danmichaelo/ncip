<?php namespace Scriptotek\Ncip;

use Illuminate\Support\ServiceProvider;

class NcipServiceProvider extends ServiceProvider {

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
	public function boot() {
		$this->package('danmichaelo/ncip');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['ncip.client'] = $this->app->share(function($app)
		{
			//\Log::info( 'URL: ' . $app['config']['ncip::url'] );
			$conn = new NcipConnector(
				$app['config']['ncip::url'], 
				$app['config']['ncip::user_agent'],
				$app['config']['ncip::agency_id']
			);
			return new NcipClient($conn);
		});

		$this->app['ncip.server'] = $this->app->share(function($app)
		{
			return new NcipServer($app['config']['ncip.agency_id']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return array('ncip.client', 'ncip.server');
	}

}
