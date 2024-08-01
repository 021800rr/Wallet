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
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FeeRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['fee:get']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['fee:post']],
)]
#[Post(
    uriTemplate: '/fees/insert/to/pln',
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
    #[Groups(['fee:get'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[Groups(['fee:get', 'fee:post', 'fee:patch'])]
    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    #[Assert\Range(
        notInRangeMessage: 'The day must be between {{ min }} and {{ max }}.',
        min: 1,
        max: 31,
    )]
    private ?int $date;

    #[Groups(['fee:get', 'fee:post', 'fee:patch'])]
    #[ORM\Column(type: "float")]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'float')]
    #[Assert\Regex(
        pattern: '/^-?\d+(\.\d{1,2})?$/',
        message: 'The amount must be a valid number with up to 2 decimal places.',
    )]
    private ?float $amount;

    #[Groups(['fee:get', 'fee:post', 'fee:patch'])]
    #[ORM\ManyToOne(targetEntity: Contractor::class, inversedBy: "fees")]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Type(type: Contractor::class)]
    private ?Contractor $contractor;

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getDate(): ?int
    {
        return $this->date;
    }

    public function setDate(?int $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(?float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getContractor(): ?Contractor
    {
        return $this->contractor;
    }

    public function setContractor(?Contractor $contractor): self
    {
        $this->contractor = $contractor;

        return $this;
    }
}
