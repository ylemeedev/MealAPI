<?php

namespace App\EventListener;

use App\Entity\PlanningRecipe;
use App\Entity\ShoppingList;
use App\Service\ShoppingListRebuilder;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: PlanningRecipe::class)]
class PlanningRecipeListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private ShoppingListRebuilder $rebuilder
    ) {}

    public function postPersist(PlanningRecipe $entity): void
    {

        $planning = $entity->getPlanning();

        if (!$planning) {
            return;
        }

        // 1. CRÉER SHOPPING LIST SI ELLE N'EXISTE PAS
        $shoppingList = $this->em->getRepository(ShoppingList::class)
            ->findOneBy(['planning' => $planning]);

        if (!$shoppingList) {
            $shoppingList = new ShoppingList();
            $shoppingList->setPlanning($planning);

            $this->em->persist($shoppingList);
            $this->em->flush();
        }

        $this->rebuilder->rebuild($planning);
    }
}
