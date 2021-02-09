<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user", "item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le username est obligatoire")
     * @Groups({"user", "item"})
     */
    private $username;

    /**
     * @ORM\OneToMany(targetEntity=Item::class, mappedBy="userItem")
     * @Groups({"user"})
     */
    private $Items;

    public function __construct()
    {
        $this->Items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->Items;
    }

    public function addItem(Item $item): self
    {
        if (!$this->Items->contains($item)) {
            $this->Items[] = $item;
            $item->setUserItem($this);
        }

        return $this;
    }

    public function removeItem(Item $item): self
    {
        if ($this->Items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getUserItem() === $this) {
                $item->setUserItem(null);
            }
        }

        return $this;
    }
}
