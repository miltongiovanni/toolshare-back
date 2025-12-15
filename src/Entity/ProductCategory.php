<?php

namespace App\Entity;

use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

#[ORM\Entity(repositoryClass: ProductCategoryRepository::class)]
class ProductCategory implements TranslatableInterface
{

    use TranslatableTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, ProductSubcategory>
     */
    #[ORM\OneToMany(targetEntity: ProductSubcategory::class, mappedBy: 'product_category')]
    private Collection $productSubcategories;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    public function __construct()
    {
        $this->productSubcategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ProductSubcategory>
     */
    public function getProductSubcategories(): Collection
    {
        return $this->productSubcategories;
    }

    public function addProductSubcategory(ProductSubcategory $productSubcategory): static
    {
        if (!$this->productSubcategories->contains($productSubcategory)) {
            $this->productSubcategories->add($productSubcategory);
            $productSubcategory->setProductCategory($this);
        }

        return $this;
    }

    public function removeProductSubcategory(ProductSubcategory $productSubcategory): static
    {
        if ($this->productSubcategories->removeElement($productSubcategory)) {
            // set the owning side to null (unless already changed)
            if ($productSubcategory->getProductCategory() === $this) {
                $productSubcategory->setProductCategory(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }
    // Translatable methods
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
