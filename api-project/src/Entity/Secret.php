<?php

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
    #[ORM\JoinColumn(nullable: false)]
    private ?Nemesis $nemesis_id = null;

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

    public function getNemesisId(): ?Nemesis
    {
        return $this->nemesis_id;
    }

    public function setNemesisId(?Nemesis $nemesis_id): static
    {
        $this->nemesis_id = $nemesis_id;

        return $this;
    }
}
