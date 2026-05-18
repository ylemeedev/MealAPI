<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\Repository\ShoppingListItemRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use App\Entity\Traits\Timestampable;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShoppingListItemRepository::class)]
#[ApiResource(
    operations: [
        new Patch(
            /* security: "is_granted('ROLE_USER')", */
            normalizationContext: ['groups' => ['shoppingListItem:write:item']],
        ),
        new GetCollection(
            /* security: "is_granted('ROLE_USER')", */
            uriTemplate: '/my_shopping_list_item',
            filters: ['my_shopping_list_item_filter']
        ),

        /* new GetCollection(
            uriTemplate: '/admin/shopping_list_items',
            security: "is_granted('ROLE_ADMIN')"
        ) */
    ]
)]
#[ORM\HasLifecycleCallbacks]
class ShoppingListItem
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingListItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient = null;

    #[ORM\Column(nullable: true)]
    private ?float $quantity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $unit = null;

    #[Groups(['planning:read:collection'])]
    #[ORM\Column]
    private ?bool $isChecked = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingListItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?shoppingList $shoppingList = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getShoppingList(): ?shoppingList
    {
        return $this->shoppingList;
    }

    public function setShoppingList(?shoppingList $shoppingList): static
    {
        $this->shoppingList = $shoppingList;

        return $this;
    }
}
