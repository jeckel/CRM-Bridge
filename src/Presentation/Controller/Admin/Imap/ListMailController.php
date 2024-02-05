<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\Imap;

use PhpImap\Mailbox;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListMailController extends AbstractController
{
    public function __construct(
        private readonly Mailbox $mailbox
    ) {}

    #[Route(
        path: '/admin/imap/check',
        name: 'imap_check',
        methods: ['GET']
    )]
    public function __invoke(): Response
    {
        $mailsIds = $this->mailbox->searchMailbox('ALL');
        dd($this->mailbox->getMail($mailsIds[0]));
        dd($mailsIds);
    }
}
