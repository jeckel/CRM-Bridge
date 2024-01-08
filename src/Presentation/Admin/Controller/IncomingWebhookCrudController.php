<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Admin\Controller;

use App\Entity\IncomingWebhook;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class IncomingWebhookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return IncomingWebhook::class;
    }
}
