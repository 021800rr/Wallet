<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\State\WalletCheckerProvider;

#[ApiResource(
    normalizationContext: ['groups' => ['account:read']],
)]
#[Get(
    uriTemplate: '/check/wallets',
    provider: WalletCheckerProvider::class,
)]
class WalletChecker extends AbstractAccountChecker
{
}
