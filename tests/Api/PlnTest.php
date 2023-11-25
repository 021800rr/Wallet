<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Pln;
use App\Tests\SetupApi;
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
            "hydra:totalItems" => 5,
            "hydra:member" => [
                [
                    "id" => 5,
                    "date" => "2021-05-13T00:00:00+00:00",
                    "amount" => -40,
                    "balance" => 100,
                    "contractor" => [
                        "description" => "Allegro"
                    ]
                ],
                [
                    "id" => 4,
                    "date" => "2021-04-13T00:00:00+00:00",
                    "amount" => -30,
                    "balance" => 140,
                    "contractor" => [
                        "description" => "Allegro"
                    ]
                ],
                [
                    "isConsistent" => true,
                    "id" => 3,
                    "date" => "2021-03-13T00:00:00+00:00",
                    "amount" => -20,
                    "balance" => 170,
                    "contractor" => [
                        "description" => "Media Expert"
                    ],
                ],
                [
                    "id" => 2,
                    "date" => "2021-02-13T00:00:00+00:00",
                    "amount" => -10,
                    "balance" => 190,
                ],
                [
                    "id" => 1,
                    "balance" => 200,
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
    public function testPost(): void
    {
        $this->client->request('POST', '/api/plns', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2021-06-13",
                "amount" => -50,
                "contractor" => "/api/contractors/5",
                "description" => "test..."
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "date" => "2021-06-13T00:00:00+00:00",
            "amount" => -50,
            "contractor" => [
                "@id" => "/api/contractors/5",
            ]
        ]);

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 6,
            "hydra:member" => [
                0 => [
                    "date" => "2021-06-13T00:00:00+00:00",
                    "balance" => 50,
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
        $this->client->request('PUT', '/api/plns/4', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2021-04-13",
                "amount" => -20,
                "contractor" => "/api/contractors/5",
                "description" => "test test"
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "date" => "2021-04-13T00:00:00+00:00",
            "amount" => -20,
            "contractor" => [
                "id" => 5
            ],
            "description" => "test test"
        ]);

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 5,
            "hydra:member" => [
                0 => [
                    "balance" => 110,
                ],
            ]
        ]);
    }

    public function testPutMoveBackward(): void
    {
        $this->client->request('PUT', '/api/plns/3', [
            'auth_bearer' => $this->token,
            'json' => [
                "date" => "2021-02-12",
            ],
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "date" => "2021-02-12T00:00:00+00:00",
        ]);

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 5,
            "hydra:member" => [
                ["id" => 5,],
                ["id" => 4,],
                [
                    "id" => 2,
                    "date" => "2021-02-13T00:00:00+00:00",
                    "amount" => -10,
                    "balance" => 170,
                ],
                [
                    "id" => 3,
                    "date" => "2021-02-12T00:00:00+00:00",
                    "amount" => -20,
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
    public function testDelete(): void
    {
        $this->client->request('DELETE', '/api/plns/4', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->plnRepository->findOneBy(['id' => 4])
        );

        $this->client->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 4,
            "hydra:member" => [
                0 => [
                    "balance" => 130,
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
