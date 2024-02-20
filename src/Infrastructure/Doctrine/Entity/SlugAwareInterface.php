<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 20/02/2024
 */

namespace App\Infrastructure\Doctrine\Entity;

interface SlugAwareInterface
{
    public function setSlug(string $slug): self;

    public function getSlugSource(): string;
}
