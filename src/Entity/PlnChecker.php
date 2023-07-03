<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\PlnCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:read']],
)]
#[Get(
    uriTemplate: '/check/plns',
    provider: PlnCheckerProvider::class,
)]
class PlnChecker extends AbstractWalletChecker
{
}
