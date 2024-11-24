<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\EurChecker;

/**
 * @extends AbstractWalletCheckerProvider<EurChecker>
 * @implements ProviderInterface<EurChecker>
 */
readonly class EurCheckerProvider extends AbstractWalletCheckerProvider implements ProviderInterface
{
    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     * @return EurChecker|EurChecker[]|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var EurChecker $eurChecker */
        $eurChecker = $this->accountChecker($this->supervisor, $this->eurRepository, new EurChecker());

        return $eurChecker;
    }
}
