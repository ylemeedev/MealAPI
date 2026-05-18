<?php

namespace App\EventListener;

use App\Entity\PlanningRecipe;
use App\Service\ShoppingListRebuilder;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::postPersist, method: 'onChange', entity: PlanningRecipe::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'onChange', entity: PlanningRecipe::class)]
#[AsEntityListener(event: Events::postRemove, method: 'onChange', entity: PlanningRecipe::class)]
class PlanningRecipeListener
{
    public function __construct(
        private ShoppingListRebuilder $rebuilder
    ) {}

    public function onChange(PlanningRecipe $entity): void
    {
        $planning = $entity->getPlanning();

        if (!$planning) {
            return;
        }

        // important: flush déjà passé → safe rebuild
        $this->rebuilder->rebuild($planning);
    }
}
