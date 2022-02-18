<?php

namespace Felrov\Drill\Tests;

use Felrov\Drill\MandrillTransport;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function it_gets_mandrill_driver()
    {
        $this->assertInstanceOf(MandrillTransport::class, $this->app->make('mailer')->getSwiftMailer()->getTransport());
    }
}
