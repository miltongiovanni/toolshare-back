<?php

namespace App\Entity;

use App\Repository\ProductCategoryRepository;
use Carbon\Carbon;
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

    #[ORM\Column(length: 255)]
    private ?string $image = null;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'image' => $this->getImage(),
            'created_at' => Carbon::parse($this->getCreatedAt())->toISOString(),
            'updated_at' => $this->getUpdatedAt() != null ? Carbon::parse($this->getUpdatedAt())->toISOString() : Carbon::parse($this->getCreatedAt())->toISOString(),
            'enabled' => $this->isEnabled(),
            ];

    }

}
