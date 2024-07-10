<?php

namespace App\Entity;

use App\Repository\NemesisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NemesisRepository::class)]
class Nemesis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $is_alive = null;

    #[ORM\Column(nullable: true)]
    private ?int $years = null;

    #[ORM\ManyToOne(inversedBy: 'nemeses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Character $character_id = null;

    #[ORM\OneToMany(targetEntity: Secret::class, mappedBy: 'nemesis_id', orphanRemoval: true)]
    private Collection $secrets;

    public function __construct()
    {
        $this->secrets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isIsAlive(): ?bool
    {
        return $this->is_alive;
    }

    public function setIsAlive(bool $is_alive): static
    {
        $this->is_alive = $is_alive;

        return $this;
    }

    public function getYears(): ?int
    {
        return $this->years;
    }

    public function setYears(?int $years): static
    {
        $this->years = $years;

        return $this;
    }

    public function getCharacterId(): ?Character
    {
        return $this->character_id;
    }

    public function setCharacterId(?Character $character_id): static
    {
        $this->character_id = $character_id;

        return $this;
    }

    /**
     * @return Collection<int, Secret>
     */
    public function getSecrets(): Collection
    {
        return $this->secrets;
    }

    public function addSecret(Secret $secret): static
    {
        if (!$this->secrets->contains($secret)) {
            $this->secrets->add($secret);
            $secret->setNemesisId($this);
        }

        return $this;
    }

    public function removeSecret(Secret $secret): static
    {
        if ($this->secrets->removeElement($secret)) {
            // set the owning side to null (unless already changed)
            if ($secret->getNemesisId() === $this) {
                $secret->setNemesisId(null);
            }
        }

        return $this;
    }
}
