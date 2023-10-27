<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    public ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Pret::class, orphanRemoval: true)]
    public Collection $prets;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Truc::class, orphanRemoval: true)]
    public Collection $trucs;

    public function __construct()
    {
        $this->prets = new ArrayCollection();
        $this->trucs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function addTruc(Truc $truc): static
    {
        if (!$this->trucs->contains($truc)) {
            $this->trucs->add($truc);
            $truc->setUser($this);
        }

        return $this;
    }

    public function removeTruc(Truc $truc): static
    {
        if ($this->trucs->removeElement($truc)) {
            // set the owning side to null (unless already changed)
            if ($truc->getUser() === $this) {
                $truc->setUser(null);
            }
        }

        return $this;
    }

}
