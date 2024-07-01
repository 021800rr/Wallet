<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\PlnCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:get']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[Get(
    uriTemplate: '/check/plns',
    provider: PlnCheckerProvider::class,
)]
class PlnChecker extends AbstractWalletChecker
{
}
