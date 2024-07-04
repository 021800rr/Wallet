<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\State\TransferProcessor;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource]
#[Post(
    uriTemplate: '/transfer/to/backup',
    denormalizationContext: ['groups' => ['transfer:post']],
    security: "is_granted('ROLE_ADMIN')",
    processor: TransferProcessor::class
)]
class TransferToBackup extends AbstractTransfer
{
    #[Groups('transfer:post')]
    #[Assert\Type(type: 'bool')]
    private bool $currency;

    public function isCurrency(): bool
    {
        return $this->currency;
    }

    public function setCurrency(bool $currency): void
    {
        $this->currency = $currency;
    }
}
