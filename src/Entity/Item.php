<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user", "item", "todolist"})
     * 
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user", "item", "todolist"})
     */
    private $title;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user", "item", "todolist"})
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Todolist::class, inversedBy="items", cascade={"persist"})
     * @Groups({"user", "item"})
     */
    private $todolist;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Items")
     * @Groups({"item"})
     */
    private $userItem;

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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTodolist(): ?Todolist
    {
        return $this->todolist;
    }

    public function setTodolist(?Todolist $todolist): self
    {
        $this->todolist = $todolist;

        return $this;
    }

    public function getUserItem(): ?User
    {
        return $this->userItem;
    }

    public function setUserItem(?User $userItem): self
    {
        $this->userItem = $userItem;

        return $this;
    }
}
