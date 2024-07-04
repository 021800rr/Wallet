<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndexNoLocale(): void
    {
        /** @var Registry $registry */
        $registry = static::getContainer()->get('doctrine');
        $userRepository = $registry->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'rr']);

        $this->assertNotNull($testUser, 'User with username "rr" not found in the database.');

        $this->client->loginUser($testUser);

        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/pl/pln/', Response::HTTP_FOUND);
    }

    public function testIndexNoLocaleNoLoggedUser(): void
    {
        $this->client->request('GET', '/');
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }
}
