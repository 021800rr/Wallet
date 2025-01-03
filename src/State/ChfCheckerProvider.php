<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ChfChecker;

/**
 * @extends AbstractWalletCheckerProvider<ChfChecker>
 * @implements ProviderInterface<ChfChecker>
 */
readonly class ChfCheckerProvider extends AbstractWalletCheckerProvider implements ProviderInterface
{
    /**
     * @param mixed[] $uriVariables
     * @param mixed[] $context
     * @return ChfChecker|ChfChecker[]|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var ChfChecker $chfChecker */
        $chfChecker = $this->accountChecker($this->supervisor, $this->chfRepository, new ChfChecker());

        return $chfChecker;
    }
}
