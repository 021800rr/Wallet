<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\PlnChecker;

readonly class PlnCheckerProvider extends AbstractWalletCheckerProvider implements ProviderInterface
{
    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->accountChecker($this->supervisor, $this->plnRepository, new PlnChecker());
    }
}
