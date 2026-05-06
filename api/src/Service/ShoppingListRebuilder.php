<?php

namespace App\Service;

use App\Entity\Planning;
use App\Entity\ShoppingList;
use Doctrine\ORM\EntityManagerInterface;

class ShoppingListRebuilder
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function rebuild(Planning $planning): void
    {
        $conn = $this->em->getConnection();

        // 1. récupérer ou créer la shopping list
        $shoppingList = $this->em->getRepository(ShoppingList::class)
            ->findOneBy(['planning' => $planning]);

        if (!$shoppingList) {
            $shoppingList = new ShoppingList();
            $shoppingList->setPlanning($planning);

            $this->em->persist($shoppingList);
            $this->em->flush(); // important pour avoir l'id
        }

        $conn->beginTransaction();

        try {
            // 2. reset items
            $conn->executeStatement("
                DELETE FROM shopping_list_item
                WHERE shopping_list_id = :id
            ", [
                'id' => $shoppingList->getId()
            ]);

            // 3. rebuild + fusion ingrédients
            $conn->executeStatement("
                INSERT INTO shopping_list_item
                (quantity, unit, is_checked, updated_at, created_at, shopping_list_id, ingredient_id)

                SELECT
                    SUM(ri.quantity) AS quantity,
                    ri.unit,
                    0,
                    NOW(),
                    NOW(),
                    sl.id,
                    ri.ingredient_id

                FROM shopping_list sl
                JOIN planning_recipe pr ON pr.planning_id = sl.planning_id
                JOIN recipe_ingredient ri ON ri.recipe_id = pr.recipe_id

                WHERE sl.id = :id

                GROUP BY sl.id, ri.ingredient_id, ri.unit
            ", [
                'id' => $shoppingList->getId()
            ]);

            $conn->commit();

        } catch (\Throwable $e) {
            $conn->rollBack();
            throw $e;
        }
    }
}