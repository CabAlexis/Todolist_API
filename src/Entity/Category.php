<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity=todolist::class, inversedBy="categories")
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
     * @return Collection|todolist[]
     */
    public function getTodolists(): Collection
    {
        return $this->todolists;
    }

    public function addTodolist(todolist $todolist): self
    {
        if (!$this->todolists->contains($todolist)) {
            $this->todolists[] = $todolist;
        }

        return $this;
    }

    public function removeTodolist(todolist $todolist): self
    {
        $this->todolists->removeElement($todolist);

        return $this;
    }
}
