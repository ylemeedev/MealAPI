<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Repository\PlanningRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Entity\Traits\Timestampable;
use App\Attribute\CurrentUser;

#[CurrentUser]
#[ORM\Entity(repositoryClass: PlanningRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_USER')",
            uriTemplate: "/my_plannings",
            filters: ['my_plannings_exists_filter'],
            normalizationContext: ['groups' => ['planning:read:collection']],
        ),
        /* new Get(
            security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['planning:read:item']],
        ) */
    ]
)]
#[ORM\HasLifecycleCallbacks]
class Planning
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['planning:read:collection'])]
    #[ORM\Column]
    private ?int $weekNumber = null;

    #[Groups(['planning:read:collection'])]
    #[ORM\Column]
    private ?int $year = null;

    #[ORM\ManyToOne(inversedBy: 'planning')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, PlanningRecipe>
     */
    #[ORM\OneToMany(targetEntity: PlanningRecipe::class, mappedBy: 'planning')]
    private Collection $planningRecipes;

    #[Groups(['planning:read:collection'])]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, ShoppingList>
     */
    #[ORM\OneToMany(targetEntity: ShoppingList::class, mappedBy: 'planning', orphanRemoval: true)]
    private Collection $shoppingLists;

    public function __construct()
    {
        $this->planningRecipes = new ArrayCollection();
        $this->shoppingLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeekNumber(): ?int
    {
        return $this->weekNumber;
    }

    public function setWeekNumber(int $weekNumber): static
    {
        $this->weekNumber = $weekNumber;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, PlanningRecipe>
     */
    public function getPlanningRecipes(): Collection
    {
        return $this->planningRecipes;
    }

    public function addPlanningRecipe(PlanningRecipe $planningRecipe): static
    {
        if (!$this->planningRecipes->contains($planningRecipe)) {
            $this->planningRecipes->add($planningRecipe);
            $planningRecipe->setPlanning($this);
        }

        return $this;
    }

    public function removePlanningRecipe(PlanningRecipe $planningRecipe): static
    {
        if ($this->planningRecipes->removeElement($planningRecipe)) {
            // set the owning side to null (unless already changed)
            if ($planningRecipe->getPlanning() === $this) {
                $planningRecipe->setPlanning(null);
            }
        }

        return $this;
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

    /**
     * @return Collection<int, ShoppingList>
     */
    public function getShoppingLists(): Collection
    {
        return $this->shoppingLists;
    }

    public function addShoppingList(ShoppingList $shoppingList): static
    {
        if (!$this->shoppingLists->contains($shoppingList)) {
            $this->shoppingLists->add($shoppingList);
            $shoppingList->setPlanning($this);
        }

        return $this;
    }

    public function removeShoppingList(ShoppingList $shoppingList): static
    {
        if ($this->shoppingLists->removeElement($shoppingList)) {
            // set the owning side to null (unless already changed)
            if ($shoppingList->getPlanning() === $this) {
                $shoppingList->setPlanning(null);
            }
        }

        return $this;
    }
}
