<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\ChfChecker;

class ChfCheckerProvider extends AbstractAccountCheckerProvider implements ProviderInterface
{
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        return $this->accountChecker($this->supervisor, $this->chfRepository, new ChfChecker());
    }
}
