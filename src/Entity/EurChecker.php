<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\EurCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:read']],
)]
#[Get(
    uriTemplate: '/check/eurs',
    provider: EurCheckerProvider::class,
)]
class EurChecker extends AbstractWalletChecker
{
}
