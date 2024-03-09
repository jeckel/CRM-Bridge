<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 29/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\WebMail;

use App\Infrastructure\Doctrine\Repository\ImapMessageRepository;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TreatedController extends AbstractController
{
    public function __construct(
        private readonly ImapMessageRepository $repository,
        private readonly Clock $clock
    ) {}

    #[Route(
        path: '/webmail/{mailId}/mark_as_treated',
        name: "webmail_mark_as_treated",
        methods: ['PUT']
    )]
    public function markAsTreated(string $mailId): Response
    {
        $this->repository->persist(
            $this->repository->getById($mailId)
                ->setIsTreated(true)
                ->setTreatedAt($this->clock->now())
        );
        return $this->redirectToRoute('webmail_mail', ['mailId' => $mailId]);
    }

    #[Route(
        path: '/webmail/{mailId}/mark_as_untreated',
        name: "webmail_mark_as_untreated",
        methods: ['PUT']
    )]
    public function markAsUntreated(string $mailId): Response
    {
        $this->repository->persist(
            $this->repository->getById($mailId)
                ->setIsTreated(false)
                ->setTreatedAt(null)
        );
        return $this->redirectToRoute('webmail_mail', ['mailId' => $mailId]);
    }
}