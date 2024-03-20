<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/02/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Domain\Entity;

use App\Component\Shared\Identity\CompanyId;

use function App\slug;

class Company
{
    private CompanyId $id;

    private string $name = '';

    private string $slug = '';

    private function __construct() {}

    public static function new(string $name): self
    {
        $company = new self();
        $company->id = CompanyId::new();
        $company->name = $name;
        $company->slug = slug($name);
        return $company;
    }

    public function getIdentity(): CompanyId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function __toString()
    {
        return $this->name;
    }
}
