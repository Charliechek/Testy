<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\OtazkaRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: OtazkaRepository::class)]
class Otazka
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $text;

    #[ORM\Column(type: 'integer')]
    private $spravnaOdpoved;

    #[ORM\ManyToOne(targetEntity: Test::class, inversedBy: 'otazky')]
    #[ORM\JoinColumn(nullable: false)]
    private $test;

    #[ORM\OneToMany(mappedBy: 'otazka', targetEntity: Odpoved::class, orphanRemoval: true, cascade: ["persist"])]
    private $odpovedi;

    public function __construct()
    {
        $this->odpovedi = new ArrayCollection();
    }

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

    public function getSpravnaOdpoved(): int
    {
        return $this->spravnaOdpoved;
    }

    public function setSpravnaOdpoved(int $spravnaOdpoved): self
    {
        $this->spravnaOdpoved = $spravnaOdpoved;

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

    /**
     * @return Collection<int, Odpoved>
     */
    public function getOdpovedi(): Collection
    {
        return $this->odpovedi;
    }

    public function addOdpovedi(Odpoved|array $odpovedi): self
    {
        if (!is_array($odpovedi)) {
            $odpovedi = [$odpovedi];
        }

        foreach ($odpovedi as $odpoved) {
            if (!$this->odpovedi->contains($odpoved)) {
                $this->odpovedi[] = $odpoved;
                $odpoved->setOtazka($this);
            }
        }

        return $this;
    }

    public function removeOdpovedi(Odpoved|array $odpovedi): self
    {
        if (!is_array($odpovedi)) {
            $odpovedi = [$odpovedi];
        }

        foreach ($odpovedi as $odpoved) {
            if ($this->odpovedi->removeElement($odpoved)) {
                if ($odpoved->getOtazka() === $this) {
                    $odpoved->setOtazka(null);
                }
            }
        }

        return $this;
    }
}
