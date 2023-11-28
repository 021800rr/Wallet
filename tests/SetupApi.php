<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait SetupApi
{
    use SetupRepos;

    private Client $client;
    private string $token;

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function setUp(): void
    {
        $this->client = self::createClient();
        $response = $this->client->request('POST', '/api/login/check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'rr',
                'password' => 'rr',
            ],
        ]);
        $json = $response->toArray();

        $this->token = $json['token'];

        $this->setUpRepos();
    }
}
