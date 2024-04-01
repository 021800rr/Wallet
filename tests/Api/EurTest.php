<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Eur;
use App\Tests\SetupApi;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class EurTest extends ApiTestCase
{
    use SetupApi;

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testGetCollection(): void
    {
        $this->client->request('GET', '/api/eurs', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Eur::class);

        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "date" => "2021-11-26T00:00:00+00:00",
                    "amount" => 40.04,
                    "balance" => 70.07,
                ],
                1 => [
                    "date" => "2021-11-04T00:00:00+00:00",
                    "amount" => 20.02,
                    "balance" => 30.03,
                ],
                2 => [
                    "date" => "2021-10-30T00:00:00+00:00",
                    "amount" => 10.01,
                    "balance" => 10.01,
                ]
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPost(): void
    {
        $this->client->request('POST', '/api/eurs', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2023-06-26",
                "amount" => 50.05,
                "contractor" => "/api/contractors/5",
                "description" => "test..."
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "date" => "2023-06-26T00:00:00+00:00",
            "amount" => 50.05,
            "contractor" => [
                "@id" => "/api/contractors/5",
            ]
        ]);

        $this->client->request('GET', '/api/eurs', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 4,
            "hydra:member" => [
                0 => [
                    "date" => "2023-06-26T00:00:00+00:00",
                    "balance" => 120.12,
                ]
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPut(): void
    {
        $this->client->request('PUT', '/api/eurs/3', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2021-11-23",
                "amount" => 40,
                "contractor" => "/api/contractors/5",
                "description" => "test test"
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "date" => "2021-11-23T00:00:00+00:00",
            "amount" => 40,
            "contractor" => [
                "id" => 5
            ],
            "description" => "test test"
        ]);

        $this->client->request('GET', '/api/eurs', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "balance" => 70.03,
                ],
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testDelete(): void
    {
        $this->client->request('DELETE', '/api/eurs/3', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->eurRepository->findOneBy(['id' => 3])
        );

        $this->client->request('GET', '/api/eurs', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 2,
            "hydra:member" => [
                0 => [
                    "balance" => 30.03,
                ],
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPatch(): void
    {
        $this->client->request('PATCH', '/api/eurs/2', [
            'auth_bearer' => $this->token,
            'json' => [
                "isConsistent" => true,
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "isConsistent" => true,
        ]);
    }
}