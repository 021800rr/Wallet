<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\SetupApi;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TransferToPlnTest extends ApiTestCase
{
    use SetupApi;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Exception
     * @see Service/Transfer/TransferTest.php
     */
    public function testPost(): void
    {
        $this->assertSame(100.00, $this->plnRepository->getCurrentBalance());

        $this->client->request('POST', '/api/transfer/to/pln', [
            'auth_bearer' => $this->token,
            'json' => [
                "amount" => 100,
                "date" => "2023-06-25"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $this->assertSame(200.00, $this->plnRepository->getCurrentBalance());

        $this->client->request('GET', '/api/backups', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:member" => [
                [
                    "retiring" => 300,
                    "holiday" => 200,
                    "amount" => -100,
                    "balance" => 500
                ],
            ]
        ]);
    }
}
