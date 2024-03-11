<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 29/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Dashboard;

use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MailSummaryController extends AbstractController
{
    #[Route(
        path: "/dashboard/embed/mail-summary/",
        name: "dashboard.embed.mail_summary",
        methods: ['GET']
    )]
    public function __invoke(EntityManagerInterface $entityManager, ImapMessageRepository $repository): Response
    {
        $count = $repository->count([]);

        $result = $entityManager->getConnection()
            ->prepare(
                "SELECT DATE(m.date) AS dt, COUNT(m.mail_id) as nb " .
                "FROM imap_message AS m " .
                "GROUP BY DATE(m.date) " .
                "ORDER BY dt"
            )->executeQuery()
            ->fetchAllKeyValue();
        return $this->render(
            'dashboard/mail_summary.html.twig',
            [
                'count' => $count,
                'mail_history' => $result
            ]
        );
    }
}
