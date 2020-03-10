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
        if (! $this->shouldRegister()) {
            return;
        }

        $this->resolveTransportManager()->extend('mandrill', function () {
            return $this->app->make('mandrill.transport');
        });
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->setupConfig();

        $this->app->bind('mandrill.transport', function () {
            $config = $this->app['config']->get('services.mandrill', []);

            return new MandrillTransport($this->guzzle($config), $config['secret'], $config['options'] ?? []);
        });

        $this->app->alias('mandrill.transport', MandrillTransport::class);
    }

    /**
     * Resolve the mail manager.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @return \Illuminate\Mail\TransportManager|\Illuminate\Mail\MailManager
     */
    public function resolveTransportManager()
    {
        if ($this->app->has('mail.manager')) {
            return $this->app->make('mail.manager');
        }

        return $this->app->make('swift.transport');
    }

    /**
     * Determine if we should register the driver.
     *
     * @return bool
     */
    protected function shouldRegister()
    {
        if ($this->app->has('mail.manager')) {
            return $this->app['config']['mail.default'] === 'mandrill';
        }

        return $this->app['config']['mail.driver'] === 'mandrill';
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

    protected function setupConfig()
    {
        $this->mergeConfigFrom(\realpath(__DIR__.'/../config/mandrill.php'), 'services.mandrill');

        if ($this->app['config']->has('mail.mailers')) {
            $this->app['config']->set('mail.mailers.mandrill', ['transport' => 'mandrill']);
        }
    }
}
