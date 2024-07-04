<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Entity\AbstractAccount;
use App\Entity\Chf;
use App\Entity\Contractor;
use App\Entity\Pln;
use App\Repository\BackupRepository;
use App\Repository\ChfRepository;
use App\Repository\ContractorRepository;
use App\Repository\EurRepository;
use App\Repository\FeeRepository;
use App\Repository\PlnRepository;
use App\Repository\UserRepository;
use App\Service\BalanceUpdater\BalanceUpdaterAccountInterface;
use App\Service\BalanceUpdater\BalanceUpdaterBackup;
use App\Service\BalanceUpdater\BalanceUpdaterWallet;
use Exception;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait SetUp
{
    private KernelBrowser $kernelBrowser;
    private Client $client;
    private string $token;
    private BackupRepository $backupRepository;
    private ChfRepository $chfRepository;
    private EurRepository $eurRepository;
    private ContractorRepository $contractorRepository;
    private FeeRepository $feeRepository;
    private PlnRepository $plnRepository;
    private Contractor $internalTransferOwner;
    private BalanceUpdaterAccountInterface $walletUpdater;
    private BalanceUpdaterAccountInterface $backupUpdater;

    /** @var Pln[] $plns */
    private array $plns;

    /** @var Chf[] $chfs */
    private array $chfs;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $client = $this->initializeClient();
        $this->setUpKernelBrowser($client);
        $this->setUpApiClient($client);
        $this->setUpRepos();
        $this->setUpAccounts();
        $this->finalizeSetup();
    }

    private function initializeClient(): null|KernelBrowser|Client
    {
        // @phpstan-ignore-next-line
        return method_exists($this, 'createClient') ? static::createClient() : null;
    }

    private function setUpKernelBrowser(null|KernelBrowser|Client $client): void
    {
        if ($client instanceof KernelBrowser) {
            $this->kernelBrowser = $client;

                /** @var UserRepository $userRepository */
            $userRepository = static::getContainer()->get(UserRepository::class);

            /** @var UserInterface $testUser */
            $testUser = $userRepository->findOneBy(['username' => 'rr']);

            $this->kernelBrowser->loginUser($testUser);
            $this->kernelBrowser->followRedirects();
        }
    }

    private function setUpApiClient(null|KernelBrowser|Client $client): void
    {
        if ($client instanceof Client) {
            $this->client = $client;

            $response = $this->client->request('POST', '/api/login/check', [
                'headers' => ['Content-Type' => 'application/json'],
                'json' => [
                    'username' => 'rr',
                    'password' => 'rr',
                ],
            ]);

            $this->token = $response->toArray()['token'];
        }
    }

    protected function setUpRepos(): void
    {
        /** @var BackupRepository $backupRepository */
        $backupRepository = static::getContainer()->get(BackupRepository::class);
        $this->backupRepository = $backupRepository;

        /** @var ChfRepository $chfRepository */
        $chfRepository = static::getContainer()->get(ChfRepository::class);
        $this->chfRepository = $chfRepository;

        /** @var EurRepository $eurRepository */
        $eurRepository = static::getContainer()->get(EurRepository::class);
        $this->eurRepository = $eurRepository;

        /** @var ContractorRepository $contractorRepository */
        $contractorRepository = static::getContainer()->get(ContractorRepository::class);
        $this->contractorRepository = $contractorRepository;

        /** @var FeeRepository $feeRepository */
        $feeRepository = static::getContainer()->get(FeeRepository::class);
        $this->feeRepository = $feeRepository;

        /** @var PlnRepository $plnRepository */
        $plnRepository = static::getContainer()->get(PlnRepository::class);
        $this->plnRepository = $plnRepository;
    }

    private function setUpAccounts(): void
    {
        /** @var Pln[] $plns */
        $plns = $this->plnRepository->getAllRecords();
        $this->plns = $plns;

        /** @var Chf[] $chfs */
        $chfs = $this->chfRepository->getAllRecords();
        $this->chfs = $chfs;
    }

    private function finalizeSetup(): void
    {
        $this->internalTransferOwner = $this->contractorRepository->getInternalTransferOwner() ?? throw new Exception('no internal transfer owner');

        $this->backupUpdater = new BalanceUpdaterBackup();
        $this->walletUpdater = new BalanceUpdaterWallet();
    }

    private function validateEntity(AbstractAccount|Chf $entity): ConstraintViolationListInterface
    {
        self::bootKernel();
        /** @var ValidatorInterface $validator */
        $validator = static::getContainer()->get(ValidatorInterface::class);

        return $validator->validate($entity);
    }
}
