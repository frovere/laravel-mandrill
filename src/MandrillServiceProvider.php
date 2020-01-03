<?php

namespace Felrov\Drill;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if ($this->app['config']['mail.driver'] !== 'mandrill') {
            return;
        }

        $this->app->make('swift.transport')->extend('mandrill', function () {
            return $this->app->make('mandrill.transport');
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(\realpath(__DIR__.'/../config/mandrill.php'), 'services.mandrill');

        $this->app->bind('mandrill.transport', function () {
            $config = $this->app['config']->get('services.mandrill', []);

            return new MandrillTransport($this->guzzle($config), $config['secret'], $config['options'] ?? []);
        });

        $this->app->alias('mandrill.transport', MandrillTransport::class);
    }

    /**
     * Get a fresh Guzzle HTTP client instance.
     */
    protected function guzzle(array $config): HttpClient
    {
        return new HttpClient(Arr::add(
            $config['guzzle'] ?? [], 'connect_timeout', 60
        ));
    }
}
