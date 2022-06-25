<?php

namespace App\Entity;

use App\Repository\ContractorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContractorRepository::class)]
#[UniqueEntity("description")]
class Contractor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank()]
    private string $description;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $account;

    #[ORM\OneToMany(mappedBy: "contractor", targetEntity: Wallet::class)]
    private Collection $wallets;

    #[ORM\OneToMany(mappedBy: "contractor", targetEntity: Backup::class)]
    private Collection $backups;

    #[ORM\OneToMany(mappedBy: "contractor", targetEntity: Fee::class)]
    private Collection $fees;

    #[Pure] public function __construct()
    {
        $this->wallets = new ArrayCollection();
        $this->backups = new ArrayCollection();
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

    public function getBackups(): Collection
    {
        return $this->backups;
    }

    public function addBackup(Backup $backup): self
    {
        if (!$this->backups->contains($backup)) {
            $this->backups[] = $backup;
            $backup->setContractor($this);
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
