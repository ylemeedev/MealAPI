<?php

namespace App\Entity\Traits;

use Symfony\Component\Serializer\Attribute\Groups;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{
    /*     #[Groups([
        'planning:read:collection'
    ])] */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    /*     #[Groups([
        'planning:read:collection'
    ])] */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();

        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
