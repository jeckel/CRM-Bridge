<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\EspoCRM\EspoAdapter;
use Espo\ApiClient\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EspoCRMContactController extends AbstractController
{
    public function __construct(private readonly EspoAdapter $espoAdapter) {}

    #[Route(
        path: '/admin/espo-crm/contacts',
        name: 'espo_crm_contacts',
        methods: ['GET']
    )]
    public function index(): Response
    {
        //        dd($this->espoAdapter->getContacts());
        return $this->render(
            'admin/espocrm/contact-list.html.twig',
            ['contacts' => $this->espoAdapter->getContacts()]
        );
    }
}
