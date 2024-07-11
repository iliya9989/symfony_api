<?php

// src/Entity/Character.php
namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
class Character
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ability = null;

    #[ORM\Column]
    private ?float $minimal_distance = null;

    #[ORM\Column(nullable: true)]
    private ?float $weight = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $born = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $in_space_since = null;

    #[ORM\Column]
    private ?int $beer_consumption = null;

    #[ORM\Column]
    private ?bool $knows_the_answer = null;

    #[ORM\OneToMany(targetEntity: Nemesis::class, mappedBy: 'character', orphanRemoval: true)]
    private Collection $nemeses;

    public function __construct()
    {
        $this->nemeses = new ArrayCollection();
    }

    // Add getters and setters for all properties
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getAbility(): ?string
    {
        return $this->ability;
    }

    public function setAbility(?string $ability): static
    {
        $this->ability = $ability;

        return $this;
    }

    public function getMinimalDistance(): ?float
    {
        return $this->minimal_distance;
    }

    public function setMinimalDistance(float $minimal_distance): static
    {
        $this->minimal_distance = $minimal_distance;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getBorn(): ?\DateTimeInterface
    {
        return $this->born;
    }

    public function setBorn(\DateTimeInterface $born): static
    {
        $this->born = $born;

        return $this;
    }

    public function getInSpaceSince(): ?\DateTimeInterface
    {
        return $this->in_space_since;
    }

    public function setInSpaceSince(?\DateTimeInterface $in_space_since): static
    {
        $this->in_space_since = $in_space_since;

        return $this;
    }

    public function getBeerConsumption(): ?int
    {
        return $this->beer_consumption;
    }

    public function setBeerConsumption(int $beer_consumption): static
    {
        $this->beer_consumption = $beer_consumption;

        return $this;
    }

    public function isKnowsTheAnswer(): ?bool
    {
        return $this->knows_the_answer;
    }

    public function setKnowsTheAnswer(bool $knows_the_answer): static
    {
        $this->knows_the_answer = $knows_the_answer;

        return $this;
    }

    /**
     * @return Collection<int, Nemesis>
     */
    public function getNemeses(): Collection
    {
        return $this->nemeses;
    }

    public function addNemesis(Nemesis $nemesis): static
    {
        if (!$this->nemeses->contains($nemesis)) {
            $this->nemeses->add($nemesis);
            $nemesis->setCharacter($this);
        }

        return $this;
    }

    public function removeNemesis(Nemesis $nemesis): static
    {
        if ($this->nemeses->removeElement($nemesis)) {
            // set the owning side to null (unless already changed)
            if ($nemesis->getCharacter() === $this) {
                $nemesis->setCharacter(null);
            }
        }

        return $this;
    }
}
