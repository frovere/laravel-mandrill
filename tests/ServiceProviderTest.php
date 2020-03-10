<?php

namespace Felrov\Drill\Tests;

use Felrov\Drill\MandrillTransport;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_gets_mandrill_driver_laravel6()
    {
        if (! $this->app->has('swift.transport')) {
            $this->markTestSkipped('swift.transport is only available for Laravel 6.0 and lower.');
        }

        $this->assertInstanceOf(MandrillTransport::class, $this->app->make('swift.transport')->driver('mandrill'));
    }

    /** @test */
    public function it_gets_mandrill_driver()
    {
        if (! $this->app->has('mail.manager')) {
            $this->markTestSkipped('mail.manager is only available for Laravel 7.0 and higher.');
        }

        $this->assertInstanceOf(MandrillTransport::class, $this->app->make('mailer')->getSwiftMailer()->getTransport());
    }
}
