<?php

namespace App\Entity;

use App\Repository\ShopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShopRepository::class)]
#[ApiResource(
    operations: []
)]
#[ORM\HasLifecycleCallbacks]
class Shop
{
    use Timestampable;
    
    #[Groups(['planning:read:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['planning:read:collection'])]
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $shopPicture = null;

    /**
     * @var Collection<int, IngredientShop>
     */
    #[ORM\OneToMany(targetEntity: IngredientShop::class, mappedBy: 'shop', orphanRemoval: true)]
    private Collection $ingredientShops;

    public function __construct()
    {
        $this->ingredientShops = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShopPicture(): ?string
    {
        return $this->shopPicture;
    }

    public function setShopPicture(?string $shopPicture): static
    {
        $this->shopPicture = $shopPicture;

        return $this;
    }

    /**
     * @return Collection<int, IngredientShop>
     */
    public function getIngredientShops(): Collection
    {
        return $this->ingredientShops;
    }

    public function addIngredientShop(IngredientShop $ingredientShop): static
    {
        if (!$this->ingredientShops->contains($ingredientShop)) {
            $this->ingredientShops->add($ingredientShop);
            $ingredientShop->setShop($this);
        }

        return $this;
    }

    public function removeIngredientShop(IngredientShop $ingredientShop): static
    {
        if ($this->ingredientShops->removeElement($ingredientShop)) {
            // set the owning side to null (unless already changed)
            if ($ingredientShop->getShop() === $this) {
                $ingredientShop->setShop(null);
            }
        }

        return $this;
    }
}
