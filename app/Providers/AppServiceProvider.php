<?php

namespace App\Providers;

use App\Events\AppealNew;
use App\Events\DPANew;
use App\Events\InvestigationClosed;
use App\Events\InvestigationNew;
use App\Events\ReportNew;
use App\Listeners\SendWebhookNotification;
use App\Mediawiki\Provider;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		RateLimiter::for( 'api', function ( Request $request ) {
			return Limit::perMinute( 60 )->by( $request->user()?->id ?: $request->ip() );
		} );

		Gate::define( 'ts', function ( User $user ) {
			return $user->hasFlag( 'ts' );
		} );

		Gate::define( 'user-manager', function ( User $user ) {
			return $user->hasFlag( 'user-manager' );
		} );

		Event::listen(
			[
				AppealNew::class,
				DPANew::class,
				InvestigationClosed::class,
				InvestigationNew::class,
				ReportNew::class,
			],
			SendWebhookNotification::class
		);

		Paginator::useBootstrap();

		Event::listen(function (SocialiteWasCalled $event) {
			$event->extendSocialite('mediawiki', Provider::class);
		});

	}
}
