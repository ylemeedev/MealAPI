<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
#[ApiResource(
    operations: []
)]
#[ORM\HasLifecycleCallbacks]
class Ingredient
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ingredientPicture = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $barcode = null;

    /**
     * @var Collection<int, RecipeIngredient>
     */
    #[ORM\OneToMany(targetEntity: RecipeIngredient::class, mappedBy: 'ingredient', orphanRemoval: true)]
    private Collection $recipeIngredients;

    /**
     * @var Collection<int, IngredientShop>
     */
    #[Groups(['planning:read:collection'])]
    #[ORM\OneToMany(targetEntity: IngredientShop::class, mappedBy: 'ingredient', orphanRemoval: true)]
    private Collection $ingredientShops;

    /**
     * @var Collection<int, ShoppingListItem>
     */
    #[ORM\OneToMany(targetEntity: ShoppingListItem::class, mappedBy: 'ingredient')]
    private Collection $shoppingListItems;

    public function __construct()
    {
        $this->recipeIngredients = new ArrayCollection();
        $this->ingredientShops = new ArrayCollection();
        $this->shoppingListItems = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIngredientPicture(): ?string
    {
        return $this->ingredientPicture;
    }

    public function setIngredientPicture(?string $ingredientPicture): static
    {
        $this->ingredientPicture = $ingredientPicture;

        return $this;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function setBarcode(?string $barcode): static
    {
        $this->barcode = $barcode;

        return $this;
    }

    /**
     * @return Collection<int, RecipeIngredient>
     */
    public function getRecipeIngredients(): Collection
    {
        return $this->recipeIngredients;
    }

    public function addRecipeIngredient(RecipeIngredient $recipeIngredient): static
    {
        if (!$this->recipeIngredients->contains($recipeIngredient)) {
            $this->recipeIngredients->add($recipeIngredient);
            $recipeIngredient->setIngredient($this);
        }

        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredient $recipeIngredient): static
    {
        if ($this->recipeIngredients->removeElement($recipeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($recipeIngredient->getIngredient() === $this) {
                $recipeIngredient->setIngredient(null);
            }
        }

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
            $ingredientShop->setIngredient($this);
        }

        return $this;
    }

    public function removeIngredientShop(IngredientShop $ingredientShop): static
    {
        if ($this->ingredientShops->removeElement($ingredientShop)) {
            // set the owning side to null (unless already changed)
            if ($ingredientShop->getIngredient() === $this) {
                $ingredientShop->setIngredient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ShoppingListItem>
     */
    public function getShoppingListItems(): Collection
    {
        return $this->shoppingListItems;
    }

    public function addShoppingListItem(ShoppingListItem $shoppingListItem): static
    {
        if (!$this->shoppingListItems->contains($shoppingListItem)) {
            $this->shoppingListItems->add($shoppingListItem);
            $shoppingListItem->setIngredient($this);
        }

        return $this;
    }

    public function removeShoppingListItem(ShoppingListItem $shoppingListItem): static
    {
        if ($this->shoppingListItems->removeElement($shoppingListItem)) {
            // set the owning side to null (unless already changed)
            if ($shoppingListItem->getIngredient() === $this) {
                $shoppingListItem->setIngredient(null);
            }
        }

        return $this;
    }
}
