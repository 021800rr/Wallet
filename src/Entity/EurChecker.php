<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\EurCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:get']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[Get(
    uriTemplate: '/check/eurs',
    provider: EurCheckerProvider::class,
)]
class EurChecker extends AbstractWalletChecker
{
}
