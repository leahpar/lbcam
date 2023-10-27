<?php

namespace App\Entity;

use App\Repository\TrucRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: TrucRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Truc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description = null;

    #[ORM\Column(length: 255)]
    public ?string $slug = null;

    #[ORM\OneToMany(mappedBy: 'truc', targetEntity: Image::class, orphanRemoval: true)]
    public Collection $images;

    #[ORM\ManyToMany(targetEntity: Tag::class, cascade: ['persist'])]
    public Collection $tags;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $publie = false;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->truc = $this;
        }
        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            $image->truc = null;
        }
        return $this;
    }

    public function getMainImage(): ?string
    {
        return count($this->images) ? $this->images->first()->filename : null;
    }

    public function __toString(): string
    {
        return $this->nom;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    #[ORM\PrePersist]
    public function setSlug(): void
    {
        $slugger = new AsciiSlugger();
        $this->slug = strtolower($slugger->slug($this->nom));
    }

}
