<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\PlnChecker;

/**
 * @extends AbstractWalletCheckerProvider<PlnChecker>
 * @implements ProviderInterface<PlnChecker>
 */
readonly class PlnCheckerProvider extends AbstractWalletCheckerProvider implements ProviderInterface
{
    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     * @return PlnChecker|PlnChecker[]|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var PlnChecker $plnChecker */
        $plnChecker = $this->accountChecker($this->supervisor, $this->plnRepository, new PlnChecker());

        return $plnChecker;
    }
}
