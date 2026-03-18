<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Import;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientFactory
{
    private HttpClientInterface $client;

    public function __construct(
        private ?string $apiSecret = null,
        private int $timeout = 4,
    ) {
        $this->client = HttpClient::create([
            'timeout' => $this->timeout,
            'headers' => [
                'TS-AUTH-TOKEN' => $this->apiSecret,
            ],
        ]);
    }

    public function getClient(): HttpClientInterface
    {
        return $this->client;
    }
}
