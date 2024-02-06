<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use App\Presentation\Form\CardDav\SetupConnectionFormType;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SetupController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationService $configuration,
        private readonly AdminUrlGenerator $urlGenerator
    ) {}

    #[Route(
        path: '/admin/carddav/setup',
        name: 'card_dav_setup',
        methods: ['GET', 'POST']
    )]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(
            type: SetupConnectionFormType::class,
            data: [
                ConfigurationKey::CARDDAV_URI->value => $this->configuration->get(ConfigurationKey::CARDDAV_URI),
                ConfigurationKey::CARDDAV_USERNAME->value => $this->configuration->get(ConfigurationKey::CARDDAV_USERNAME)
            ]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array<string, string> $formData */
            $formData = $form->getData();
            foreach ($formData as $key => $value) {
                $this->configuration->set(ConfigurationKey::from($key), $value);
            }
            return $this->redirect(
                $this->urlGenerator->setRoute(
                    'card_dav_list'
                )->generateUrl()
            );
        }

        return $this->render(
            'admin/page/form.html.twig',
            [
                'page_title' => 'card_dav.title.setup_connection',
                'form' => $form->createView()
            ]
        );
    }
}
