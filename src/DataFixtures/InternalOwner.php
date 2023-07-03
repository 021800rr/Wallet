<?php

namespace App\DataFixtures;

use App\Entity\Contractor;

trait InternalOwner
{
    public function getInternalOwner(): Contractor
    {
        /** @var Contractor $internalOwner */
        $internalOwner = $this->getReference(ContractorFixtures::INTERNAL);

        return $internalOwner;
    }
}
