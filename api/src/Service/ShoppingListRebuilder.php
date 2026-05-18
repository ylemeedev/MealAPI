<?php

namespace App\Service;

use App\Entity\Planning;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingListRebuilder
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function rebuild(Planning $planning): void
    {
        $conn = $this->em->getConnection();

        $conn->beginTransaction();

        try {
            // 1. récupérer ou créer la shopping list liée à la semaine
            $shoppingListId = $conn->fetchOne("
                SELECT id
                FROM shopping_list
                WHERE week_number = :week
                  AND year = :year
                  AND user_id = :user
                LIMIT 1
            ", [
                'week' => $planning->getWeekNumber(),
                'year' => $planning->getYear(),
                'user' => $planning->getUser()->getId(),
            ]);

            if (!$shoppingListId) {
                $conn->executeStatement("
                    INSERT INTO shopping_list (week_number, year, user_id)
                    VALUES (:week, :year, :user)
                ", [
                    'week' => $planning->getWeekNumber(),
                    'year' => $planning->getYear(),
                    'user' => $planning->getUser()->getId(),
                ]);

                $shoppingListId = $conn->lastInsertId();
            }

            // 2. reset items
            $conn->executeStatement("
                DELETE FROM shopping_list_item
                WHERE shopping_list_id = :list
            ", [
                'list' => $shoppingListId
            ]);

            // 3. rebuild items depuis planning_recipe
            $conn->executeStatement("
                INSERT INTO shopping_list_item
                (quantity, unit, is_checked, created_at, updated_at, ingredient_id, shopping_list_id)

                SELECT
                    SUM(ri.quantity) AS quantity,
                    ri.unit,
                    0,
                    NOW(),
                    NOW(),
                    ri.ingredient_id,
                    :list

                FROM planning_recipe pr
                JOIN recipe_ingredient ri ON ri.recipe_id = pr.recipe_id
                WHERE pr.planning_id = :planning

                GROUP BY ri.ingredient_id, ri.unit
            ", [
                'planning' => $planning->getId(),
                'list' => $shoppingListId
            ]);

            $conn->commit();

        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}