<?php

namespace App\Doctrine;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use App\Attribute\CurrentUser;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;

final class CurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private Security $security) {}

    private function apply(QueryBuilder $qb, string $resourceClass): void
{
    $reflection = new \ReflectionClass($resourceClass);

    $attributes = $reflection->getAttributes(\App\Attribute\CurrentUser::class);

    if (!$attributes) {
        return;
    }

    /** @var \App\Attribute\CurrentUser $config */
    $config = $attributes[0]->newInstance();

    $user = $this->security->getUser();

    if (!$user) {
        return;
    }

    // 🔥 ADMIN BYPASS
    if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
        return;
    }

    // 🔥 OWNER MODE (IMPORTANT FIX)
    $alias = $qb->getRootAliases()[0];

    $qb->andWhere("$alias.user = :userId")
       ->setParameter('userId', $user->getId());
}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        $operationName = null,
        array $context = []
    ): void {
        $this->apply($queryBuilder, $resourceClass);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        $operationName = null,
        array $context = []
    ): void {
        $this->apply($queryBuilder, $resourceClass);
    }
}