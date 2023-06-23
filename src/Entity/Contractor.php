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
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContractorRepository::class)]
#[UniqueEntity("description")]
#[ApiResource(
    normalizationContext: ['groups' => ['contractor:read']],
)]
#[GetCollection]
#[Post(
    denormalizationContext: ['groups' => ['contractor:create']],
)]
#[Patch(
    denormalizationContext: ['groups' => ['contractor:patch']],
)]
#[Delete]
class Contractor
{
    #[Groups(['contractor:read', 'account:read', 'fee:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[Groups(['contractor:read', 'contractor:create', 'contractor:patch', 'account:read', 'fee:read'])]
    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank()]
    private string $description;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $account;

    #[ORM\OneToMany(mappedBy: "contractor", targetEntity: Wallet::class)]
    private Collection $wallets;

    #[ORM\OneToMany(mappedBy: "contractor", targetEntity: Fee::class)]
    private Collection $fees;

    public function __construct()
    {
        $this->wallets = new ArrayCollection();
        $this->fees = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    public function addWallet(Wallet $wallet): self
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets[] = $wallet;
            $wallet->setContractor($this);
        }

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
