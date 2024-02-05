<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 05/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Controller\Admin\CardDav;

use App\Infrastructure\Configuration\ConfigurationKey;
use App\Infrastructure\Configuration\ConfigurationService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method addFlash(string $type, string $message)
 * @method redirect(string $url, int $status = 302): Response
 */
trait ConfigurationCheckTrait
{
    private function checkRequiredConfiguration(
        ConfigurationService $configuration,
        AdminUrlGenerator $urlGenerator
    ): ?Response {
        if (! $configuration->has(
            ConfigurationKey::CARDDAV_URI,
            ConfigurationKey::CARDDAV_USERNAME,
            ConfigurationKey::CARDDAV_PASSWORD
        )) {
            $this->addFlash(
                type: 'warning',
                message: 'card_dav.alert.missing_required_configuration'
            );
            return $this->redirect(
                $urlGenerator->setRoute(
                    'card_dav_setup'
                )->generateUrl()
            );
        }

        if (! $configuration->has(ConfigurationKey::CARDDAV_DEFAULT_ADDRESS_BOOK)) {
            $this->addFlash(
                type: 'warning',
                message: 'card_dav.alert.default_address_book_not_set'
            );
            return $this->redirect(
                $urlGenerator->setRoute(
                    'carddav_select_address_book'
                )->generateUrl()
            );
        }
        return null;
    }
}
