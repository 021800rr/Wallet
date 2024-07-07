<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bundle\FrameworkBundle\KernelBrowser as WebClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    private WebClient $webClient;

    protected function setUp(): void
    {
        $this->webClient = static::createClient();
    }

    public function testIndexNoLocale(): void
    {
        /** @var Registry $registry */
        $registry = static::getContainer()->get('doctrine');
        $userRepository = $registry->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['username' => 'rr']);

        $this->assertNotNull($testUser, 'User with username "rr" not found in the database.');

        $this->webClient->loginUser($testUser);

        $this->webClient->request('GET', '/');
        $this->assertResponseRedirects('/pl/pln/', Response::HTTP_FOUND);
    }

    public function testIndexNoLocaleNoLoggedUser(): void
    {
        $this->webClient->request('GET', '/');
        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
    }
}
