<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\ChfCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:get']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[Get(
    uriTemplate: '/check/chfs',
    provider: ChfCheckerProvider::class,
)]
class ChfChecker extends AbstractWalletChecker
{
}
