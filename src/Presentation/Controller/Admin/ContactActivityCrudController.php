<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 26/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\ContactActivity;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactActivityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ContactActivity::class;
    }
}
