<?php

namespace App\Entity;

use App\Enum\DayOfWeek;
use App\Enum\MealType;
use App\Enum\TimeOfDay;
use App\Repository\PlanningRecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Entity\Traits\Timestampable;
use App\State\PlanningRecipeCalendarProvider;

#[ORM\Entity(repositoryClass: PlanningRecipeRepository::class)]
#[ApiResource(
    operations: [
        /*         new GetCollection(
            //security: "is_granted('ROLE_USER')",
            uriTemplate: "/my-planning-recipe",
            filters: ['my_plannings_recipe_filter'],
            normalizationContext: ['groups' => ['planningRecipe:read:collection']]
        ), */

        new Get(
            uriTemplate: '/planning-week/{id}',
            provider: PlanningRecipeCalendarProvider::class,
            formats: ['json' => ['application/json']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['planningRecipe:write:item']]
        )
        /* new GetCollection(
            //security: "is_granted('ROLE_USER')",
            normalizationContext: ['groups' => ['planningRecipe:read:collection']],
        ), */
    ],

)]
#[ORM\HasLifecycleCallbacks]
class PlanningRecipe
{
    use Timestampable;

    #[Groups(['planningRecipe:read:collection'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['planningRecipe:read:collection', 'planningRecipe:write:item'])]
    #[ORM\Column(enumType: TimeOfDay::class)]
    private ?TimeOfDay $timeOfDay = null;
    
    #[Groups(['planningRecipe:write:item'])]
    #[ORM\ManyToOne(inversedBy: 'planningRecipes')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Planning $planning = null;

    #[Groups(['planningRecipe:write:item'])]
    #[ORM\ManyToOne(
        inversedBy: 'planningRecipes',
        cascade: ['persist']
    )]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Recipe $recipe = null;

    #[Groups(['planningRecipe:read:collection', 'planningRecipe:write:item'])]
    #[ORM\Column(enumType: DayOfWeek::class)]
    private ?DayOfWeek $dayOfWeek = null;

    #[Groups(['planningRecipe:read:collection', 'planningRecipe:write:item'])]
    #[ORM\Column(enumType: MealType::class)]
    private ?MealType $mealType = null;

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

    public function getMealType(): ?MealType
    {
        return $this->mealType;
    }

    public function setMealType(MealType $mealType): static
    {
        $this->mealType = $mealType;

        return $this;
    }
}
