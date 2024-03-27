<?php

/**
 * @authore Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 27/03/2024 11:18
 */
declare(strict_types=1);

namespace App\Presentation\Setup\Controller;

use App\Component\WebMail\Infrastructure\Doctrine\Repository\ImapAccountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_ADMIN")]
class ImapAccountIndexAction extends AbstractController
{
    #[Route('/setup/imap/', name: 'setup.imap.index')]
    public function __invoke(ImapAccountRepository $repository): Response
    {
        return $this->render(
            '@setup/imap/index_embed.html.twig',
            [
                'imap_accounts' => $repository->findAll()
            ]
        );
    }
}
