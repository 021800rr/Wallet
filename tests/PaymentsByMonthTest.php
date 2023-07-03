<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PaymentsByMonthTest extends ApiTestCase
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
        $r = $this->client->request('GET', '/api/backups/payments/by/month', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "backups" => [
                [
                    "yearMonth" => "2021-06",
                    "sum_of_amounts" => "300"
                ],
                [
                    "yearMonth" => "2021-05",
                    "sum_of_amounts" => "300"
                ]
            ],
            "expected" => 300,
            "plnBalance" => 170,
            "chfBalance" => 70.07,
            "backupLastRecord" => [
                "balance" => 600
            ],
            "total" => 770
        ]);
    }
}
