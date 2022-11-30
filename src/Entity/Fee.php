<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\ApiFeeController;
use App\Repository\FeeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FeeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['fee:read']],
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['fee:create']],
)]
#[Post(
    uriTemplate: '/fee/insert/to/wallet',
    controller: ApiFeeController::class,
    input: false,
    output: false,
    name: 'insert',
)]
#[Patch(
    denormalizationContext: ['groups' => ['fee:patch']],
)]
#[Delete]
class Fee
{
    #[Groups(['fee:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[Groups(['fee:read', 'fee:create', 'fee:patch'])]
    #[ORM\Column(type: "integer")]
    private int $date;

    #[Groups(['fee:read', 'fee:create', 'fee:patch'])]
    #[ORM\Column(type: "float")]
    private float $amount;

    #[Groups(['fee:read', 'fee:create', 'fee:patch'])]
    #[ORM\ManyToOne(targetEntity: Contractor::class, inversedBy: "fees")]
    #[ORM\JoinColumn(nullable: false)]
    private Contractor $contractor;

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): int
    {
        return $this->date;
    }

    public function setDate(int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getContractor(): Contractor
    {
        return $this->contractor;
    }

    public function setContractor(Contractor $contractor): self
    {
        $this->contractor = $contractor;

        return $this;
    }
}
