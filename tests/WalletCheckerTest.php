<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class WalletCheckerTest extends ApiTestCase
{
    use SetupApi;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGet(): void
    {
        $r = $this->client->request('GET', '/api/check/wallets', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "result" => "Error",
            "accounts" => [
                [
                    "balance_supervisor" => 190,
                    "id" => 2,
                    "balance" => 191,
                    "contractor" => [
                        "description" => "Allegro"
                    ]
                ],
                [
                    "balance_supervisor" => 171,
                    "id" => 3,
                    "balance" => 170,
                    "contractor" => [
                        "description" => "Allegro"
                    ]
                ]
            ]
        ]);
    }
}
