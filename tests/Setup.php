<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait Setup
{
    protected Client $client;
    protected string $token;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    protected function setUp(): void
    {
        $this->client = self::createClient();
        $response = $this->client->request('POST', '/api/login_check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => 'rr',
                'password' => 'rr',
            ],
        ]);
        $json = $response->toArray();

        $this->token = $json['token'];
    }
}
