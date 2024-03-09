<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 10:00
 */
declare(strict_types=1);

namespace App\Presentation\Controller;

use App\Presentation\Service\Avatar\ChainAvatarProvider;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AvatarController extends AbstractController
{
    #[Route('/avatar/{emailB64}', name: 'avatar')]
    public function __invoke(string $emailB64, ChainAvatarProvider $avatarProvider, string $projectDir): Response
    {
        $email = base64_decode($emailB64, true);
        if ($email === false) {
            throw new LogicException('Unable to decode email');
        }
        $avatar = $avatarProvider->getAvatar($email);
        if (null === $avatar) {
            return $this->returnContent(
                $projectDir . '/assets/default_avatar.svg',
                'image/svg+xml'
            );
        }
        return $this->returnContent(
            $avatar->url,
            $avatar->mimeType
        );
    }

    protected function returnContent(string $path, string $mimeType): Response
    {
        $content = file_get_contents($path);
        if (false === $content) {
            throw new LogicException('Unable to read file');
        }
        // Set the appropriate MIME type in the response
        $response = new Response();
        $response->headers->set('Content-Type', $mimeType);

        // Set the image content as the response body
        return $response->setContent($content);
    }
}
