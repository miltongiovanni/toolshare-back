<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements TranslatableInterface
{

    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    // Métodos traducibles
    public function getName(): ?string
    {
        return $this->translate(null, false)->getName();
    }

    public function setName(string $name): self
    {
        $this->translate(null, false)->setName($name);
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->translate(null, false)->getDescription();
    }

    public function setDescription(string $description): self
    {
        $this->translate(null, false)->setDescription($description);
        return $this;
    }
}
