<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"category"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre est obligatoire")
     * @Groups({"category"})
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Todolist::class, mappedBy="category")
     * @Groups({"category"})
     */
    private $todolists;

    public function __construct()
    {
        $this->todolists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection|Todolist[]
     */
    public function getTodolists(): Collection
    {
        return $this->todolists;
    }

    public function addTodolist(Todolist $todolist): self
    {
        if (!$this->todolists->contains($todolist)) {
            $this->todolists[] = $todolist;
            $todolist->setCategory($this);
        }

        return $this;
    }

    public function removeTodolist(Todolist $todolist): self
    {
        if ($this->todolists->removeElement($todolist)) {
            // set the owning side to null (unless already changed)
            if ($todolist->getCategory() === $this) {
                $todolist->setCategory(null);
            }
        }

        return $this;
    }
}
