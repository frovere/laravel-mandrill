<?php

namespace Felrov\Drill\Tests;

use Felrov\Drill\MandrillTemplateMailable;
use Felrov\Drill\MandrillTemplate;
use Felrov\Drill\MandrillTransport;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class TransportTest extends TestCase
{
    private $httpContainer = [];

    /** @test */
    public function it_uses_template_endpoint_if_provided()
    {
        $config = $this->app['config']->get('services.mandrill');
        $this->app->instance('mandrill.transport', new MandrillTransport($this->fakeGuzzle(), $config['secret'], []));

        $template = new MandrillTemplate('template-slug');

        Mail::to('recipient.email@example.com')
            ->send($this->templateMailable($template));

        tap($this->httpContainer[0]['request'], function (Request $request) {
            $this->assertEquals('https://mandrillapp.com/api/1.0/messages/send-template.json', (string) $request->getUri());
            $jsonRequest = \json_decode($request->getBody()->getContents());
            $this->assertEquals('template-slug', $jsonRequest->template_name);
        });
    }

    /** @test */
    public function it_uses_raw_endpoint_by_default()
    {
        $config = $this->app['config']->get('services.mandrill');
        $this->app->instance('mandrill.transport', new MandrillTransport($this->fakeGuzzle(), $config['secret'], $config['options']));

        Mail::to('recipient.email@example.com')
            ->send($this->mailable());

        tap($this->httpContainer[0]['request'], function (Request $request) {
            $this->assertEquals('https://mandrillapp.com/api/1.0/messages/send-raw.json', (string) $request->getUri());
            $jsonRequest = \json_decode($request->getBody()->getContents());
            $this->assertStringContainsString('TEST MAIL', $jsonRequest->raw_message);
        });
    }

    private function fakeGuzzle(): HttpClient
    {
        $history = Middleware::history($this->httpContainer);

        $handlerStack = HandlerStack::create(new MockHandler([
            new Response(200, [], '[{"email": "recipient.email@example.com", "status": "sent", "reject_reason": "hard-bounce", "_id": "abc123abc123abc123abc123abc123"}]'),
        ]));
        $handlerStack->push($history);

        return new HttpClient(['handler' => $handlerStack]);
    }

    private function mailable()
    {
        return new class extends Mailable {
            public function build()
            {
                $this->html('TEST MAIL');
            }
        };
    }

    private function templateMailable($template)
    {
        return new class($template) extends MandrillTemplateMailable {};
    }
}
