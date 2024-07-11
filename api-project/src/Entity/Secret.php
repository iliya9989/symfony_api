<?php

// src/Entity/Secret.php
namespace App\Entity;

use App\Repository\SecretRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SecretRepository::class)]
class Secret
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $secret_code = null;

    #[ORM\ManyToOne(inversedBy: 'secrets')]
    #[ORM\JoinColumn(name: 'nemesis_id', referencedColumnName: 'id', nullable: false)]
    private ?Nemesis $nemesis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecretCode(): ?int
    {
        return $this->secret_code;
    }

    public function setSecretCode(int $secret_code): static
    {
        $this->secret_code = $secret_code;

        return $this;
    }

    public function getNemesis(): ?Nemesis
    {
        return $this->nemesis;
    }

    public function setNemesis(?Nemesis $nemesis): static
    {
        $this->nemesis = $nemesis;

        return $this;
    }
}
