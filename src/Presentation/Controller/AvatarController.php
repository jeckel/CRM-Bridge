<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:00
 */
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Infrastructure\Doctrine\Repository\ContactRepository;
use App\Presentation\Service\Avatar\AvatarDtoInterface;
use App\Presentation\Service\Avatar\Provider\ChainAvatarProvider;
use App\Presentation\Service\Avatar\RemoteAvatarDto;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;
use Symfony\Component\Routing\Attribute\Route;

class AvatarController extends AbstractController
{
    public function __construct(
        private readonly string $projectDir
    ) {}

    #[Route('/avatar/{emailB64}', name: 'avatar')]
    public function emailAvatar(string $emailB64, ChainAvatarProvider $avatarProvider): Response
    {
        $email = base64_decode($emailB64, true);
        if ($email === false) {
            throw new LogicException('Unable to decode email');
        }
        return $this->returnContent(
            $avatarProvider->getAvatarFromEmail($email)
        );
    }

    #[Route('/avatar/contact/{contactId}', name: 'avatar.contact')]
    public function contactAvatar(string $contactId, ChainAvatarProvider $avatarProvider, ContactRepository $contactRepository): Response
    {
        return $this->returnContent(
            $avatarProvider->getAvatarFromContact(
                $contactRepository->getById($contactId)
            )
        );
    }

    protected function returnContent(?AvatarDtoInterface $avatarDto): Response
    {
        if (null === $avatarDto) {
            $avatarDto = new RemoteAvatarDto(
                url: $this->projectDir . '/assets/default_avatar.svg',
                mimeType: 'image/svg+xml'
            );
        }

        // Set the appropriate MIME type in the response
        $response = new Response(
            content: $avatarDto->getContent(),
            status: 200,
            headers: [
                'Content-Type' => $avatarDto->getMimeType(),
                'Cache-Control' => 'public, max-age=604800'
            ]
        );
        // Disable cache settings override when session exists
        // @see https://symfony.com/doc/current/http_cache.html#http-caching-and-user-sessions
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true');
        return $response;
    }
}
