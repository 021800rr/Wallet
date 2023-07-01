<?php

namespace App\DataFixtures;

use App\Entity\Contractor;

trait InternalOwner
{
    public function getInternalOwner(): Contractor
    {
        /** @var Contractor $contractor */
        $contractor = $this->getReference(ContractorFixtures::INTERNAL);

        return $contractor;
    }
}
