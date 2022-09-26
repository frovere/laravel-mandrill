<?php

namespace Felrov\Drill;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class MandrillServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (!$this->shouldRegister()) {
            return;
        }

        $this->resolveTransportManager()->extend('mandrill', fn () => $this->app->make('mandrill.transport'));
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->setupConfig();

        $this->app->bind('mandrill.http-client', fn () => null);

        $this->app->bind('mandrill.transport', function ($app) {
            $config = $app['config']->get('services.mandrill', []);

            if (isset($config['scheme']) && $config['scheme'] === 'smtp') {
                throw new \InvalidArgumentException('Use SMTP Laravel Driver');
            }

            return (new MandrillTransportFactory(null, $this->app->make('mandrill.http-client'), null))->create(
                new Dsn(
                    'mandrill+'.($config['scheme'] ?? 'https'),
                    $config['endpoint'] ?? 'default',
                    $config['secret'],
                    null,
                    null,
                    $config['options'] ?? [],
                )
            );
        });

        $this->app->alias('mandrill.transport', MandrillTransport::class);
    }

    /**
     * Resolve the mail manager.
     *
     * @return \Illuminate\Mail\MailManager
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function resolveTransportManager()
    {
        return $this->app->make('mail.manager');
    }

    /**
     * Determine if we should register the driver.
     *
     * @return bool
     */
    protected function shouldRegister()
    {
        return $this->app['config']['mail.default'] === 'mandrill';
    }

    protected function setupConfig(): void
    {
        $this->mergeConfigFrom(\realpath(__DIR__.'/../config/mandrill.php'), 'services.mandrill');

        if ($this->app['config']->has('mail.mailers')) {
            $this->app['config']->set('mail.mailers.mandrill', ['transport' => 'mandrill']);
        }
    }
}
