<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\SetUp;

class PaymentsByMonthTest extends ApiTestCase
{
    use SetUp;

    public function testGet(): void
    {
        $this->client->request('GET', '/api/backups/payments/by/month', ['auth_bearer' => $this->token]);
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
            "plnBalance" => 100,
            "chfBalance" => 70.07,
            "backupLastRecord" => [
                "balance" => 600
            ],
            "total" => 700
        ]);
    }
}
