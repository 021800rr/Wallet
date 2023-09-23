<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\EurChecker;

readonly class EurCheckerProvider extends AbstractWalletCheckerProvider implements ProviderInterface
{
    /**
     * @param array<mixed, mixed> $uriVariables
     * @param array<mixed, mixed> $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->accountChecker($this->supervisor, $this->eurRepository, new EurChecker());
    }
}
