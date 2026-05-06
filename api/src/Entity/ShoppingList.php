<?php

namespace App\Entity;

use App\Repository\ShoppingListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\Traits\Timestampable;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;

#[ORM\Entity(repositoryClass: ShoppingListRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['shoppingList:read:collection']],
        ),
    ],
)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: "uniq_planning_shopping_list", columns: ["planning_id"])]
class ShoppingList
{
    use Timestampable;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shoppingLists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Planning $planning = null;

    /**
     * @var Collection<int, ShoppingListItem>
     */
    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\OneToMany(targetEntity: ShoppingListItem::class, mappedBy: 'shoppingList')]
    private Collection $shoppingListItems;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[Groups(['planning:read:collection', 'shoppingList:read:collection'])]
    #[ORM\Column(nullable: true)]
    private ?float $budget = null;

    public function __construct()
    {
        $this->shoppingListItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlanning(): ?Planning
    {
        return $this->planning;
    }

    public function setPlanning(?Planning $planning): static
    {
        $this->planning = $planning;

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
            $shoppingListItem->setShoppingList($this);
        }

        return $this;
    }

    public function removeShoppingListItem(ShoppingListItem $shoppingListItem): static
    {
        if ($this->shoppingListItems->removeElement($shoppingListItem)) {
            // set the owning side to null (unless already changed)
            if ($shoppingListItem->getShoppingList() === $this) {
                $shoppingListItem->setShoppingList(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setBudget(?float $budget): static
    {
        $this->budget = $budget;

        return $this;
    }
}
