<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ContractorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContractorRepository::class)]
#[UniqueEntity("description")]
#[ApiResource(
    normalizationContext: ['groups' => ['contractor:get']],
    security: "is_granted('ROLE_ADMIN')"
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['contractor:post']],
)]
#[Patch(
    denormalizationContext: ['groups' => ['contractor:patch']],
)]
#[Delete]
class Contractor
{
    #[Groups(['contractor:get', 'account:get', 'fee:get'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[Groups(['contractor:get', 'contractor:post', 'contractor:patch', 'account:get', 'fee:get'])]
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    private string $description;

    #[Groups(['contractor:get', 'contractor:post', 'contractor:patch'])]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 255)]
    private ?string $account;

    #[ORM\OneToMany(mappedBy: 'contractor', targetEntity: Fee::class)]
    #[Assert\Type(type: Collection::class)]
    private Collection $fees;

    public function __construct()
    {
        $this->fees = new ArrayCollection();
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAccount(): ?string
    {
        return $this->account;
    }

    public function setAccount(?string $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getFees(): Collection
    {
        return $this->fees;
    }

    public function addFee(Fee $fee): self
    {
        if (!$this->fees->contains($fee)) {
            $this->fees[] = $fee;
            $fee->setContractor($this);
        }

        return $this;
    }
}
