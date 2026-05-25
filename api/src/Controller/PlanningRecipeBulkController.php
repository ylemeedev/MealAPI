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
use App\Service\ShoppingListRebuilder;

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

        if (!isset($items['planningId'])) {
            return new JsonResponse([
                'error' => 'Missing planningId'
            ], 400);
        }

        try {

            // Recherche du planning
            $planning = $em
                ->getRepository(Planning::class)
                ->find($items['planningId']);

            // Si le planning n'existe pas, on le crée
            if (!$planning) {

                $planning = new Planning();
                $planning->setName('Mon planning');

                $planning->setWeekNumber(
                    (int) date('W')
                );

                $planning->setYear(
                    (int) date('Y')
                );

                // User connecté
                $planning->setUser($this->getUser());

                $em->persist($planning);
            }

            // Suppression des anciennes recettes du planning
            $existingPlanningRecipes = $em
                ->getRepository(PlanningRecipe::class)
                ->findBy([
                    'planning' => $planning
                ]);

            foreach ($existingPlanningRecipes as $pr) {
                $em->remove($pr);
            }

            foreach ($items['planning'] as $day => $meals) {
                foreach ($meals as $timeOfDay => $courses) {

                    foreach ($courses as $mealType => $recipeName) {

                        // Recherche recette existante
                        $recipe = $em
                            ->getRepository(Recipe::class)
                            ->findOneBy([
                                'name' => $recipeName
                            ]);

                        // Création recette si inexistante
                        if (!$recipe) {
                            $recipe = new Recipe();
                            $recipe->setName($recipeName);

                            $em->persist($recipe);
                        }

                        $planningRecipe = new PlanningRecipe();

                        $planningRecipe->setPlanning($planning);

                        $planningRecipe->setRecipe($recipe);

                        $planningRecipe->setDayOfWeek(
                            DayOfWeek::from($day)
                        );

                        $planningRecipe->setMealType(
                            MealType::from($mealType)
                        );

                        $planningRecipe->setTimeOfDay(
                            TimeOfDay::from($timeOfDay)
                        );

                        $em->persist($planningRecipe);
                    }
                }
            }

            $em->flush();

            return new JsonResponse([
                'success' => true
            ]);
        } catch (\Exception $e) {

            return new JsonResponse([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
