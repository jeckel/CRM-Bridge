<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 22/03/2024
 */

declare(strict_types=1);

namespace App\Presentation\Contact\Controller;

use App\Component\Shared\Identity\ContactId;
use App\Presentation\Contact\Query\ContactActivitiesListQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactActivityListAction extends AbstractController
{
    #[Route(
        path: "/contact/{contactId}/activities",
        name: "contact.activities",
        methods: ['GET']
    )]
    public function __invoke(
        string $contactId,
        Request $request,
        ContactActivitiesListQuery $query
    ): Response {
        $page = $request->query->getInt('page', 1);
        $limit = 25;
        $activities = $query(ContactId::from($contactId), $page, $limit);
        return $this->render(
            '@contact/activities.html.twig',
            [
                'activities' => $activities,
                'page' => $page,
                'limit' => $limit,
                'total' => $activities->getTotalItemCount()
            ]
        );
    }
}
