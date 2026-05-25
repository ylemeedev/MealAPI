<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\PlanningRecipeRepository;

class PlanningRecipeCalendarProvider implements ProviderInterface
{
    public function __construct(
        private PlanningRecipeRepository $repository
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $planningId = $uriVariables['id'] ?? null;

        if (!$planningId) {
            throw new \InvalidArgumentException('Planning id is required');
        }

        $items = $this->repository->findByPlanning($planningId);

        $result = [];

        foreach ($items as $item) {
            $day = $item->getDayOfWeek()->value;
            $time = $item->getTimeOfDay()->value;
            $meal = $item->getMealType()->value;

            /*             $result[$day][$time][$meal][] = [
                '@id' => "/api/planning_recipes/" . $item->getId(),
                'id' => $item->getId(),
                'timeOfDay' => $time,
                'dayOfWeek' => $day,
                'mealType' => $meal,
                'recipe' => [
                    '@id' => "/api/recipes/" . $item->getRecipe()->getId(),
                    'id' => $item->getRecipe()->getId(),
                    'name' => $item->getRecipe()->getName(),
                ],
            ]; */
            $result[$day][$time][$meal] = $item->getRecipe()->getName();
        }

        return $result;
    }
}
