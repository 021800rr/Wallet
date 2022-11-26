<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\ChfCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:read']],
)]
#[Get(
    uriTemplate: '/check/chfs',
    provider: ChfCheckerProvider::class,
)]
class ChfChecker extends AbstractAccountChecker
{
}
