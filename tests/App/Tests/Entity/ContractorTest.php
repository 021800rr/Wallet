<?php

namespace App\Tests\Entity;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Contractor;
use App\Tests\SetUp;

class ContractorTest extends ApiTestCase
{
    use SetUp;

    public function testGetCollection(): void
    {
        $this->apiClient->request('GET', '/api/contractors', ['auth_bearer' => $this->token]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertMatchesResourceCollectionJsonSchema(Contractor::class);

        $this->assertJsonContains([
            "hydra:totalItems" => 5,
            "hydra:member" => [
                0 => [
                    "id" => 1,
                    "description" => "Media Expert"
                ],
                1 => [
                    "id" => 2,
                    "description" => "Allegro"
                ],
                2 => [
                    "id" => 3,
                    "description" => "Netflix"
                ],
                3 => [
                    "id" => 4,
                    "description" => "Spotify"
                ],
                4 => [
                    "id" => 5,
                    "description" => "Przelew własny"
                ]
            ]
        ]);
    }

    public function testPost(): void
    {
        $this->apiClient->request('POST', '/api/contractors', [
            'auth_bearer' => $this->token,
            'json' => [
                "description" => "HBO"
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "description" => "HBO"
        ]);

        $this->apiClient->request('GET', '/api/contractors', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 6,
        ]);
    }

    public function testDelete(): void
    {
        $this->apiClient->request('POST', '/api/contractors', [
            'auth_bearer' => $this->token,
            'json' => [
                "description" => "HBO"
            ]
        ]);
        /** @var string $iri */
        $iri = $this->findIriBy(Contractor::class, ['description' => 'HBO']);

        $this->apiClient->request('DELETE', $iri, ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->contractorRepository->findOneBy(['description' => 'HBO'])
        );
    }

    public function testPatch(): void
    {
        $this->apiClient->request('PATCH', '/api/contractors/2', [
            'auth_bearer' => $this->token,
            'json' => [
                "description" => "test",
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            "description" => "test",
        ]);
    }
}
