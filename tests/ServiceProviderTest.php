<?php

namespace Felrov\Drill\Tests;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillApiTransport;
use Symfony\Component\Mailer\Bridge\Mailchimp\Transport\MandrillHttpTransport;

class ServiceProviderTest extends TestCase
{
    #[Test]
    public function it_gets_mandrill_https_driver(): void
    {
        $this->app['config']->set('services.mandrill.scheme', 'https');
        $this->assertInstanceOf(MandrillHttpTransport::class, $this->app->make('mailer')->getSymfonyTransport());
    }

    #[Test]
    public function it_gets_mandrill_api_driver(): void
    {
        $this->app['config']->set('services.mandrill.scheme', 'api');
        $this->assertInstanceOf(MandrillApiTransport::class, $this->app->make('mailer')->getSymfonyTransport());
    }
}
