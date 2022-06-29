<?php

namespace App\Entity;

use App\Repository\TestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TestRepository::class)]
class Test
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nazev;

    #[ORM\Column(type: 'datetime')]
    private $casVytvoreni;

    #[ORM\OneToMany(mappedBy: 'test', targetEntity: Otazka::class, orphanRemoval: true)]
    private $otazky;

    public function __construct()
    {
        $this->otazky = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNazev(): ?string
    {
        return $this->nazev;
    }

    public function setNazev(string $nazev): self
    {
        $this->nazev = $nazev;

        return $this;
    }

    public function getCasVytvoreni(): ?\DateTimeInterface
    {
        return $this->casVytvoreni;
    }

    public function setCasVytvoreni(\DateTimeInterface $casVytvoreni): self
    {
        $this->casVytvoreni = $casVytvoreni;

        return $this;
    }

    /**
     * @return Collection<int, Otazka>
     */
    public function getOtazky(): Collection
    {
        return $this->otazky;
    }

    public function addOtazky(Otazka|array $otazky): self
    {
        if (!is_array($otazky)) {
            $otazky = [$otazky];
        }

        foreach ($otazky as $otazka) {
            if (!$this->otazky->contains($otazka)) {
                $this->otazky[] = $otazka;
                $otazka->setTest($this);
            }
        }

        return $this;
    }

    public function removeOtazky(Otazka $otazky): self
    {
        if (!is_array($otazky)) {
            $otazky = [$otazky];
        }

        foreach ($otazky as $otazka) {
            if ($this->otazky->removeElement($otazka)) {
                if ($otazka->getTest() === $this) {
                    $otazka->setTest(null);
                }
            }
        }

        return $this;
    }
}
