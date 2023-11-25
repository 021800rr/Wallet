<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Contractor;
use App\Tests\SetupApi;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ContractorTest extends ApiTestCase
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
        $this->client->request('GET', '/api/contractors', ['auth_bearer' => $this->token]);
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

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testPost(): void
    {
        $this->client->request('POST', '/api/contractors', [
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

        $this->client->request('GET', '/api/contractors', ['auth_bearer' => $this->token]);
        $this->assertJsonContains([
            "hydra:totalItems" => 6,
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testDelete(): void
    {
        $this->client->request('POST', '/api/contractors', [
            'auth_bearer' => $this->token,
            'json' => [
                "description" => "HBO"
            ]
        ]);
        /** @var string $iri */
        $iri = $this->findIriBy(Contractor::class, ['description' => 'HBO']);

        $this->client->request('DELETE', $iri, ['auth_bearer' => $this->token]);
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
            $this->contractorRepository->findOneBy(['description' => 'HBO'])
        );
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
        $this->client->request('PATCH', '/api/contractors/2', [
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
