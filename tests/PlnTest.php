<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Pln;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PlnTest extends ApiTestCase
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
        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Pln::class);

        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                [
                    "id" => 3,
                    "date" => "2021-05-13T00:00:00+00:00",
                    "amount" => -20,
                    "balance" => 170,
                    "contractor" => [
                        "description" => "Allegro"
                    ]
                ],
                [
                    "id" => 2,
                    "date" => "2021-05-12T00:00:00+00:00",
                    "amount" => -10,
                    "balance" => 191,
                    "contractor" => [
                        "description" => "Allegro"
                    ]
                ],
                [
                    "isConsistent" => true,
                    "id" => 1,
                    "date" => "2021-05-10T00:00:00+00:00",
                    "amount" => -1,
                    "balance" => 200,
                    "contractor" => [
                        "description" => "Media Expert"
                    ],
                    "description" => "z przeniesienia"
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
        $this->client->request('POST', '/api/plns', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2023-06-26",
                "amount" => -50,
                "contractor" => "/api/contractors/5",
                "description" => "test..."
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "date" => "2023-06-26T00:00:00+00:00",
            "amount" => -50,
            "contractor" => [
                "@id" => "/api/contractors/5",
            ]
        ]);

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 4,
            "hydra:member" => [
                0 => [
                    "date" => "2023-06-26T00:00:00+00:00",
                    "balance" => 120,
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
        $this->client->request('PUT', '/api/plns/2', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2021-05-11",
                "amount" => -20,
                "contractor" => "/api/contractors/5",
                "description" => "test test"
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "date" => "2021-05-11T00:00:00+00:00",
            "amount" => -20,
            "contractor" => [
                "id" => 5
            ],
            "description" => "test test"
        ]);

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 3,
            "hydra:member" => [
                0 => [
                    "balance" => 160,
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
        $this->client->request('DELETE', '/api/plns/2', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->plnRepository->findOneBy(['id' => 2])
        );

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 2,
            "hydra:member" => [
                0 => [
                    "balance" => 180,
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
        $this->client->request('PATCH', '/api/plns/2', [
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
