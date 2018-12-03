<?php
namespace Mikkame\QiitaSocialiteProvider;

use Illuminate\Support\ServiceProvider;

class QiitaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'qiita',
            function ($app) use ($socialite) {
                $config = $app['config']['services.qiita'];
                return $socialite->buildProvider(QiitaProvider::class, $config);
            }
        );
    }
}
