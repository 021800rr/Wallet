<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\Client as ApiClient;
use App\Entity\AbstractAccount;
use App\Entity\AbstractTransfer;
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
use App\Tests\Exception\InternalTransferOwnerNotFoundException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser as WebClient;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait SetUp
{
    private const string DEFAULT_USERNAME = 'rr';
    private const string DEFAULT_PASSWORD = 'rr';

    private WebClient $webClient;
    private ApiClient $apiClient;
    private BackupRepository $backupRepository;
    private ChfRepository $chfRepository;
    private EurRepository $eurRepository;
    private ContractorRepository $contractorRepository;
    private FeeRepository $feeRepository;
    private PlnRepository $plnRepository;
    private Contractor $internalTransferOwner;
    private BalanceUpdaterAccountInterface $walletUpdater;
    private BalanceUpdaterAccountInterface $backupUpdater;
    private string $token;
    private string $username;

    /** @var Pln[] $plns */
    private array $plns;

    /** @var Chf[] $chfs */
    private array $chfs;

    /** @throws InternalTransferOwnerNotFoundException */
    protected function traitSetUp(): void
    {
        $this->username = getenv('TEST_USERNAME') ?: self::DEFAULT_USERNAME;

        $client = $this->initializeClient();
        $this->setUpWebClient($client);
        $this->setUpApiClient($client);
        $this->setUpRepositories();
        $this->setUpAccounts();
        $this->setUpInternalTransferOwner();
        $this->setUpBalanceUpdaters();
    }

    private function initializeClient(): null|WebClient|ApiClient
    {
        // @phpstan-ignore-next-line
        return method_exists($this, 'createClient') ? static::createClient() : null;
    }

    private function setUpWebClient(null|WebClient|ApiClient $client): void
    {
        if (!$client instanceof WebClient) {
            return;
        }
        $this->webClient = $client;

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        /** @var ?UserInterface $testUser */
        $testUser = $userRepository->findOneBy(['username' => $this->username]);

        if ($testUser === null) {
            throw new \RuntimeException(sprintf("Test user '%s' not found", $this->username));
        }

        $this->webClient->loginUser($testUser);
        $this->webClient->followRedirects();
    }

    private function setUpApiClient(null|WebClient|ApiClient $client): void
    {
        if (!$client instanceof ApiClient) {
            return;
        }
        $this->apiClient = $client;

        $password = getenv('TEST_PASSWORD') ?: self::DEFAULT_PASSWORD;

        $response = $this->apiClient->request('POST', '/api/login/check', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'username' => $this->username,
                'password' => $password,
            ],
        ]);

        $responseData = $response->toArray();
        if (!isset($responseData['token'])) {
            throw new \RuntimeException('API login failed: token not found in response');
        }
        $this->token = $responseData['token'];
    }

    protected function setUpRepositories(): void
    {
        // @phpstan-ignore-next-line
        $this->backupRepository = static::getContainer()->get(BackupRepository::class);
        // @phpstan-ignore-next-line
        $this->chfRepository = static::getContainer()->get(ChfRepository::class);
        // @phpstan-ignore-next-line
        $this->eurRepository = static::getContainer()->get(EurRepository::class);
        // @phpstan-ignore-next-line
        $this->contractorRepository = static::getContainer()->get(ContractorRepository::class);
        // @phpstan-ignore-next-line
        $this->feeRepository = static::getContainer()->get(FeeRepository::class);
        // @phpstan-ignore-next-line
        $this->plnRepository = static::getContainer()->get(PlnRepository::class);
    }

    private function setUpAccounts(): void
    {
        // @phpstan-ignore-next-line
        $this->plns = $this->plnRepository->getAllRecords();
        // @phpstan-ignore-next-line
        $this->chfs = $this->chfRepository->getAllRecords();
    }

    /** @throws InternalTransferOwnerNotFoundException */
    private function setUpInternalTransferOwner(): void
    {
        $this->internalTransferOwner = $this->contractorRepository->getInternalTransferOwner()
            ?? throw new InternalTransferOwnerNotFoundException('Internal transfer owner not found during setup.');
    }

    private function setUpBalanceUpdaters(): void
    {
        $this->backupUpdater = new BalanceUpdaterBackup();
        $this->walletUpdater = new BalanceUpdaterWallet();
    }

    private function validateEntity(AbstractAccount|AbstractTransfer|Chf $entity): ConstraintViolationListInterface
    {
        if (!self::$booted) {
            self::bootKernel();
        }
        /** @var ValidatorInterface $validator */
        $validator = static::getContainer()->get(ValidatorInterface::class);

        return $validator->validate($entity);
    }
}
