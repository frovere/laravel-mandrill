<?php

namespace Felrov\Drill;

use GuzzleHttp\ClientInterface;
use Illuminate\Mail\Transport\Transport;
use Psr\Http\Message\ResponseInterface;
use Swift_Mime_SimpleMessage;

class MandrillTransport extends Transport
{
    /**
     * The Mandrill API endpoint.
     */
    protected const API_ENDPOINT = 'https://mandrillapp.com/api/1.0/messages';

    /**
     * Guzzle client instance.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * The Mandrill API key.
     *
     * @var string
     */
    protected $key;

    /**
     * Mandrill options
     *
     * @var array
     */
    protected $options;

    /**
     * Create a new Mandrill transport instance.
     */
    public function __construct(ClientInterface $client, string $key, array $options)
    {
        $this->key = $key;
        $this->client = $client;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $this->request($message);

        $this->sendPerformed($message);

        return $this->numberOfRecipients($message);
    }

    /**
     * Get all the addresses this message should be sent to.
     *
     * Note that Mandrill still respects CC, BCC headers in raw message itself.
     */
    protected function getTo(Swift_Mime_SimpleMessage $message): array
    {
        $to = [];

        if ($message->getTo()) {
            $to = \array_merge($to, \array_keys($message->getTo()));
        }

        if ($message->getCc()) {
            $to = \array_merge($to, \array_keys($message->getCc()));
        }

        if ($message->getBcc()) {
            $to = \array_merge($to, \array_keys($message->getBcc()));
        }

        return $to;
    }

    /**
     * Get all the addresses this message should be sent to.
     */
    protected function getToTemplate(Swift_Mime_SimpleMessage $message): array
    {
        $to = [];

        if ($message->getTo()) {
            $to = \array_merge($to, \array_map(function ($email, $name) {
                return [
                    'email' => $email,
                    'name' => $name,
                    'type' => 'to',
                ];
            }, \array_keys($message->getTo()), $message->getTo()));
        }

        if ($message->getCc()) {
            $to = \array_merge($to, \array_map(function ($email, $name) {
                return [
                    'email' => $email,
                    'name' => $name,
                    'type' => 'cc',
                ];
            }, \array_keys($message->getCc()), $message->getCc()));
        }

        if ($message->getBcc()) {
            $to = \array_merge($to, \array_map(function ($email, $name) {
                return [
                    'email' => $email,
                    'name' => $name,
                    'type' => 'bcc',
                ];
            }, \array_keys($message->getBcc()), $message->getBcc()));
        }

        return $to;
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function request(Swift_Mime_SimpleMessage $message): ResponseInterface
    {
        if ($template = $this->getTemplate($message->getBody())) {
            $from = $message->getFrom();

            return $this->client->request('POST', self::API_ENDPOINT.'/send-template.json', [
                'json' => [
                    'key' => $this->key,
                    'template_name' => $template->id(),
                    'template_content' => $template->templateContent(),
                    'message' => \array_filter([
                        'from_email' => \key($from),
                        'from_name' => \reset($from) ?: null,
                        'to' => $this->getToTemplate($message),
                        'preserve_recipients' => false,
                        'merge_languague' => $template->mergeLanguage(),
                        'global_merge_vars' => $template->globalMergeVars(),
                        'merge_vars' => $template->mergeVars(),
                    ]),
                    'async' => $this->options['async'] ?? null,
                    'ip_pool' => $this->options['ip_pool'] ?? null,
                ],
            ]);
        }

        return $this->client->request('POST', self::API_ENDPOINT.'/send-raw.json', [
            'json' => [
                'key' => $this->key,
                'to' => $this->getTo($message),
                'raw_message' => $message->toString(),
                'async' => $this->options['async'] ?? null,
                'ip_pool' => $this->options['ip_pool'] ?? null,
            ],
        ]);
    }

    /**
     * Get the MandrillTemplate if set
     */
    protected function getTemplate(string $data): ?MandrillTemplate
    {
        $data = @\unserialize($data);

        if ($data !== false && ! \is_null($data) && $data instanceof MandrillTemplate) {
            return $data;
        }

        return null;
    }
}
