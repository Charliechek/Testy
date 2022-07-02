<?php

namespace App\Entity;

use App\Repository\HistorieTestuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistorieTestuRepository::class)]
class HistorieTestu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $cas;

    #[ORM\Column(type: 'integer')]
    private $pocetOtazek;

    #[ORM\Column(type: 'integer')]
    private $pocetSpravnychOdpovedi;

    #[ORM\ManyToOne(targetEntity: Test::class, inversedBy: 'Historie')]
    private $test;

    #[ORM\ManyToOne(targetEntity: Uzivatel::class, inversedBy: 'historieTestu')]
    #[ORM\JoinColumn(nullable: false)]
    private $uzivatel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCas(): ?\DateTimeInterface
    {
        return $this->cas;
    }

    public function setCas(\DateTimeInterface $cas): self
    {
        $this->cas = $cas;

        return $this;
    }

    public function getPocetOtazek(): ?int
    {
        return $this->pocetOtazek;
    }

    public function setPocetOtazek(int $pocetOtazek): self
    {
        $this->pocetOtazek = $pocetOtazek;

        return $this;
    }

    public function getPocetSpravnychOdpovedi(): ?int
    {
        return $this->pocetSpravnychOdpovedi;
    }

    public function setPocetSpravnychOdpovedi(int $pocetSpravnychOdpovedi): self
    {
        $this->pocetSpravnychOdpovedi = $pocetSpravnychOdpovedi;

        return $this;
    }

    public function getTest(): ?Test
    {
        return $this->test;
    }

    public function setTest(?Test $test): self
    {
        $this->test = $test;

        return $this;
    }

    public function getUzivatel(): ?Uzivatel
    {
        return $this->uzivatel;
    }

    public function setUzivatel(?Uzivatel $uzivatel): self
    {
        $this->uzivatel = $uzivatel;

        return $this;
    }
}
