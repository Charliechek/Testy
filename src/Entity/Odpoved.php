<?php

namespace App\Entity;

use App\Repository\OdpovedRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OdpovedRepository::class)]
class Odpoved
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $text;

    #[ORM\ManyToOne(targetEntity: Otazka::class, inversedBy: 'odpovedi')]
    #[ORM\JoinColumn(nullable: false)]
    private $otazka;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getOtazka(): ?Otazka
    {
        return $this->otazka;
    }

    public function setOtazka(?Otazka $otazka): self
    {
        $this->otazka = $otazka;

        return $this;
    }
}
