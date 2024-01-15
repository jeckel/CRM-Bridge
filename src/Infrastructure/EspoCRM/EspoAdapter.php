<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\EspoCRM;

use App\Infrastructure\EspoCRM\Model\Contact;
use Espo\ApiClient\Client;
use stdClass;

/**
 * @see https://docs.espocrm.com/development/api/
 */
class EspoAdapter
{
    public function __construct(private readonly Client $espoClient) {}

    /**
     * @return iterable<Contact>
     * @throws \Espo\ApiClient\Exception\Error
     * @throws \Espo\ApiClient\Exception\ResponseError
     */
    public function getContacts(): iterable
    {
        /** @var stdClass $content */
        $content = json_decode($this->espoClient->request(Client::METHOD_GET, 'Contact')->getBodyPart());

        foreach($content->list as $contact) {
            yield new Contact($contact);
        };
    }

    public function getContactByEmail(string $email): ?Contact
    {
        foreach($this->getContacts() as $contact) {
            if($contact->emailAddress === $email) {
                return $contact;
            }
        }

        return null;
    }
}
