<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 10/01/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin;

use App\Infrastructure\Doctrine\Entity\User;
use App\Infrastructure\LinkedIn\LinkedInClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LinkedInController extends AbstractController
{
    public function __construct(private readonly LinkedInClient $linkedInClient) {}

    #[Route(
        path: '/admin/linkedin',
        name: 'linkedin',
        methods: ['GET']
    )]
    public function index(): Response
    {
        // Rediriger vers LinkedIn
        return $this->redirect(
            $this->linkedInClient->getRedirectionUri()
        );
    }

    #[Route(
        path: '/linkedin/callback',
        methods: ['GET', 'POST']
    )]
    public function callback(Request $request): Response
    {
        if ($request->get('state') === $_SESSION['csrf_token'] && $request->get('code') !== null) {
            /** @var string $code */
            $code = $request->get('code');
            $this->linkedInClient->exchangeCodeToAccessToken($code);
            return $this->redirectToRoute('linkedin_contact');
        }
        dd($request->get('error'), $request->get('error_description'), $request->get('state'), $request->get('code'), $_SESSION['csrf_token']);
    }

    #[Route(
        path: '/linkedin/contact',
        methods: ['GET', 'POST'],
        name: 'linkedin_contact'
    )]
    public function refresh(Security $security): Response
    {
        /** @var User $user */
        $user = $security->getUser();
        $api_url = "https://api.linkedin.com/v2/userinfo"; // Ajustez 'count' si nécessaire
        $headers = [
            "Authorization: Bearer {$user->getLinkedInAccessToken()}",
            "Cache-Control: no-cache",
            "X-Restli-Protocol-Version: 2.0.0"
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $contacts = curl_exec($curl);
        curl_close($curl);

        dd(json_decode((string) $contacts));
    }
}
