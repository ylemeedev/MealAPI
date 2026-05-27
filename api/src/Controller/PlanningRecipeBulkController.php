<?php

namespace App\Controller;

use App\Entity\Planning;
use App\Entity\PlanningRecipe;
use App\Entity\Recipe;
use App\Enum\DayOfWeek;
use App\Enum\MealType;
use App\Enum\TimeOfDay;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlanningRecipeBulkController extends AbstractController
{
    #[Route('/api/planning-recipes/bulk', methods: ['POST'])]
    public function __invoke(
        Request $request,
        EntityManagerInterface $em,
    ): JsonResponse {

        $items = json_decode($request->getContent(), true);

        if (!$items) {
            return new JsonResponse([
                'error' => 'Invalid JSON'
            ], 400);
        }

        if (!isset($items['planning'])) {
            return new JsonResponse([
                'error' => 'Missing planning'
            ], 400);
        }

        try {

            $planningId = $items['planningId'] ?? null;

            $planning = null;

            if ($planningId) {
                $planning = $em
                    ->getRepository(Planning::class)
                    ->find($planningId);
            }

            $isNew = !$planning;

            // CREATE
            if ($isNew) {

                if (!isset($items['weekNumber']) || !isset($items['year'])) {
                    return new JsonResponse([
                        'error' => 'weekNumber and year are required for new planning'
                    ], 400);
                }

                $planning = new Planning();

                $planning->setName(
                    $items['name'] ?? ('Semaine ' . $items['weekNumber'] . ' ' . $items['year'])
                );

                $planning->setWeekNumber((int) $items['weekNumber']);
                $planning->setYear((int) $items['year']);

                $planning->setUser($this->getUser());

                $em->persist($planning);
            }

            // UPDATE
            if (!$isNew) {

                if (isset($items['name'])) {
                    $planning->setName($items['name']);
                }
            }

            // Suppression anciennes recettes
            $existingPlanningRecipes = $em
                ->getRepository(PlanningRecipe::class)
                ->findBy([
                    'planning' => $planning
                ]);

            foreach ($existingPlanningRecipes as $pr) {
                $em->remove($pr);
            }

            // Création nouvelles relations
            foreach ($items['planning'] as $day => $meals) {
                foreach ($meals as $timeOfDay => $courses) {

                    foreach ($courses as $mealType => $recipeName) {

                        if (!$recipeName) {
                            continue;
                        }

                        $recipe = $em
                            ->getRepository(Recipe::class)
                            ->findOneBy([
                                'name' => $recipeName
                            ]);

                        if (!$recipe) {
                            $recipe = new Recipe();
                            $recipe->setName($recipeName);
                            $em->persist($recipe);
                        }

                        $planningRecipe = new PlanningRecipe();

                        $planningRecipe->setPlanning($planning);
                        $planningRecipe->setRecipe($recipe);

                        $planningRecipe->setDayOfWeek(DayOfWeek::from($day));
                        $planningRecipe->setMealType(MealType::from($mealType));
                        $planningRecipe->setTimeOfDay(TimeOfDay::from($timeOfDay));

                        $em->persist($planningRecipe);
                    }
                }
            }

            $em->flush();

            return new JsonResponse([
                'success' => true,
                'planningId' => $planning->getId()
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}