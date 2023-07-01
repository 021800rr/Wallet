<?php

namespace App\Tests\Controller;

use App\Entity\Contractor;
use App\Repository\ContractorRepository;
use App\Repository\UserRepository;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

trait ControllerSetup
{
    protected Contractor $contractor;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->client = static::createClient();
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        /** @var UserInterface $testUser */
        $testUser = $userRepository->findOneBy(['username' => 'rr']); //Username('rr');
        $this->client->loginUser($testUser);
        $this->client->followRedirects();

        /** @var ContractorRepository $contractorRepository */
        $contractorRepository = static::getContainer()->get(ContractorRepository::class);
        $this->contractor = $contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');

        parent::setUp();
    }
}
