<?php

namespace App\Entity;

use App\Repository\UzivatelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UzivatelRepository::class)]
class Uzivatel implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $jmeno;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToMany(mappedBy: 'uzivatel', targetEntity: HistorieTestu::class, orphanRemoval: true)]
    private $historieTestu;

    public function __construct()
    {
        $this->historieTestu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJmeno(): ?string
    {
        return $this->jmeno;
    }

    public function setJmeno(string $jmeno): self
    {
        $this->jmeno = $jmeno;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->jmeno;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function zasifrujHeslo(): void
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
    }

    /**
     * @return Collection<int, HistorieTestu>
     */
    public function getHistorieTestu(): Collection
    {
        return $this->historieTestu;
    }

    public function addHistorieTestu(HistorieTestu $historieTestu): self
    {
        if (!$this->historieTestu->contains($historieTestu)) {
            $this->historieTestu[] = $historieTestu;
            $historieTestu->setUzivatel($this);
        }

        return $this;
    }

    public function removeHistorieTestu(HistorieTestu $historieTestu): self
    {
        if ($this->historieTestu->removeElement($historieTestu)) {
            // set the owning side to null (unless already changed)
            if ($historieTestu->getUzivatel() === $this) {
                $historieTestu->setUzivatel(null);
            }
        }

        return $this;
    }
}
