<?php

namespace App\Entity;

use App\Enum\DayOfWeek;
use App\Enum\RecipeType;
use App\Enum\TimeOfDay;
use App\Repository\PlanningRecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\Timestampable;

#[ORM\Entity(repositoryClass: PlanningRecipeRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['planningRecipe:write:item']]
        ),
        new GetCollection(
            //security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['planningRecipe:read:collection']],
        ),
    ],

)]
#[ORM\HasLifecycleCallbacks]
class PlanningRecipe
{
    use Timestampable;

    #[Groups(['planning:read:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['planning:read:collection', 'planningRecipe:write:item'])]
    #[ORM\Column(enumType: TimeOfDay::class)]
    private ?TimeOfDay $timeOfDay = null;
    
    #[Groups(['planningRecipe:write:item'])]
    #[ORM\ManyToOne(inversedBy: 'planningRecipes')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Planning $planning = null;
    
    #[Groups(['planning:read:collection', 'planningRecipe:write:item'])]
    #[ORM\ManyToOne(inversedBy: 'planningRecipes')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Recipe $recipe = null;
    
    #[Groups(['planning:read:collection', 'planningRecipe:write:item'])]
    #[ORM\Column(enumType: DayOfWeek::class)]
    private ?DayOfWeek $dayOfWeek = null;
    
    #[Groups(['planning:read:collection', 'planningRecipe:write:item'])]
    #[ORM\Column(enumType: RecipeType::class)]
    private ?RecipeType $mealType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeOfDay(): ?TimeOfDay
    {
        return $this->timeOfDay;
    }

    public function setTimeOfDay(TimeOfDay $timeOfDay): static
    {
        $this->timeOfDay = $timeOfDay;

        return $this;
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

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getDayOfWeek(): ?DayOfWeek
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(DayOfWeek $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;

        return $this;
    }

    public function getMealType(): ?RecipeType
    {
        return $this->mealType;
    }

    public function setMealType(RecipeType $mealType): static
    {
        $this->mealType = $mealType;

        return $this;
    }
}
