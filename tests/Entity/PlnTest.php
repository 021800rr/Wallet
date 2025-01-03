<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Pln;
use App\Tests\SetUp;

class PlnTest extends ApiTestCase
{
    use SetUp;

    protected function setUp(): void
    {
        parent::setUp();
        $this->traitSetUp();
    }

    public function testGetCollection(): void
    {
        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Pln::class);

        $this->assertJsonContains([
            "totalItems" => 5,
            "member" => [
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

    public function testPost(): void
    {
        $this->apiClient->request('POST', '/api/plns', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                "date" => "2021-06-13",
                "amount" => -50,
                "contractor" => "/api/contractors/5",
                "description" => "test..."
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "totalItems" => 6,
            "member" => [
                0 => [
                    "date" => "2021-06-13T00:00:00+00:00",
                    "balance" => 50,
                ]
            ]
        ]);
    }

    public function testPut(): void
    {
        $this->apiClient->request('PUT', '/api/plns/4', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                "date" => "2021-04-13",
                "amount" => -20,
                "contractor" => "/api/contractors/5",
                "description" => "test test"
            ],
        ]);
        $this->assertResponseIsSuccessful();

        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "totalItems" => 5,
            "member" => [
                0 => [
                    "balance" => 110,
                ],
            ]
        ]);
    }

    public function testPutMoveBackward(): void
    {
        $this->apiClient->request('PUT', '/api/plns/3', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                "date" => "2021-02-12",
                "amount" => -20,
                "contractor" => "/api/contractors/1",
            ],
        ]);
        $this->assertResponseIsSuccessful();

        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "totalItems" => 5,
            "member" => [
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

    public function testDelete(): void
    {
        $this->apiClient->request('DELETE', '/api/plns/4', ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->plnRepository->findOneBy(['id' => 4])
        );

        $this->apiClient->request('GET', '/api/plns', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "totalItems" => 4,
            "member" => [
                0 => [
                    "balance" => 130,
                ],
            ]
        ]);
    }

    public function testPatch(): void
    {
        $this->apiClient->request('PATCH', '/api/plns/2', [
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
