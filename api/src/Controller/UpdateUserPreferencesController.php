<?php

namespace App\Controller;

use App\Dto\UpdateUserPreferencesDto;
use App\Entity\User;
use App\Entity\UserPreference;
use App\Repository\PreferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UpdateUserPreferencesController extends AbstractController
{
    public function __invoke(
        UpdateUserPreferencesDto $dto,
        PreferenceRepository $preferenceRepository,
        EntityManagerInterface $em
    ) {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedException();
        }

        $preferences = $preferenceRepository->findBy([
            'id' => $dto->preferences
        ]);

        foreach ($user->getUserPreferences() as $userPreference) {
            $em->remove($userPreference);
        }

        $em->flush();

        foreach ($preferences as $preference) {
            $userPreference = new UserPreference();
            $userPreference->setUser($user);
            $userPreference->setPreference($preference);
            $userPreference->setCreatedAt(new \DateTimeImmutable());

            $em->persist($userPreference);
        }

        $em->flush();

        return $user;
    }
}
