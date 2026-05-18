<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use App\Entity\Traits\Timestampable;
use ApiPlatform\Metadata\ApiResource;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[ApiResource(
    operations: []
)]
#[ORM\HasLifecycleCallbacks]
class Recipe
{
    use Timestampable;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $recipePicture = null;
    
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;
    
    /**
     * @var Collection<int, PlanningRecipe>
    */
    #[ORM\OneToMany(targetEntity: PlanningRecipe::class, mappedBy: 'recipe')]
    private Collection $planningRecipes;
    
    /**
     * @var Collection<int, RecipeIngredient>
    */
    #[ORM\OneToMany(targetEntity: RecipeIngredient::class, mappedBy: 'recipe', orphanRemoval: true)]
    private Collection $recipeIngredients;

    public function __construct()
    {
        $this->planningRecipes = new ArrayCollection();
        $this->recipeIngredients = new ArrayCollection();
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

    public function getRecipePicture(): ?string
    {
        return $this->recipePicture;
    }

    public function setRecipePicture(?string $recipePicture): static
    {
        $this->recipePicture = $recipePicture;

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
            $planningRecipe->setRecipe($this);
        }

        return $this;
    }

    public function removePlanningRecipe(PlanningRecipe $planningRecipe): static
    {
        if ($this->planningRecipes->removeElement($planningRecipe)) {
            // set the owning side to null (unless already changed)
            if ($planningRecipe->getRecipe() === $this) {
                $planningRecipe->setRecipe(null);
            }
        }

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
            $recipeIngredient->setRecipe($this);
        }

        return $this;
    }

    public function removeRecipeIngredient(RecipeIngredient $recipeIngredient): static
    {
        if ($this->recipeIngredients->removeElement($recipeIngredient)) {
            // set the owning side to null (unless already changed)
            if ($recipeIngredient->getRecipe() === $this) {
                $recipeIngredient->setRecipe(null);
            }
        }

        return $this;
    }
}
