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

        // ADMIN BYPASS GLOBAL
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return;
        }

        $alias = $qb->getRootAliases()[0];

        switch ($config->mode) {
            case 'none':
                return;
            case 'owner':
            default:
                $qb->andWhere("$alias.user = :user")
                    ->setParameter('user', $user);
                return;
        }
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
