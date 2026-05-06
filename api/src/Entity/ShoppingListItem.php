<?php

namespace App\Entity;

use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\Timestampable;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShoppingListItemRepository::class)]
#[ApiResource()]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(
    name: "uniq_shopping_item",
    columns: ["shopping_list_id", "ingredient_id", "unit"]
)]
class ShoppingListItem
{
    use Timestampable;
    
    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingListItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShoppingList $shoppingList = null;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\ManyToOne(inversedBy: 'shoppingListItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient = null;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Column(nullable: true)]
    private ?float $quantity = null;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unit = null;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Column]
    private ?bool $isChecked = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShoppingList(): ?ShoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?ShoppingList $shoppingList): static
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }

    public function getIngredient(): ?Ingredient
    {
        return $this->ingredient;
    }

    public function setIngredient(?Ingredient $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(?float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getIsChecked(): ?bool
    {
        return $this->isChecked;
    }

    public function setIsChecked(bool $isChecked): static
    {
        $this->isChecked = $isChecked;

        return $this;
    }

}
