<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 15/01/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\EspoCRM\Model;

use stdClass;

/**
 * @property string $id
 * @property string $name
 * @property bool $deleted
 * @property ?string $salutationName
 * @property string $firstName
 * @property string $lastName
 * @property string $title
 * @property ?string $description
 * @property string $emailAddress
 * @property string $phoneNumber
 * @property bool $doNotCall
 * @property ?string $addressStreet
 * @property ?string $addressCity
 * @property ?string $addressState
 * @property ?string $addressCountry
 * @property ?string $addressPostalCode
 * @property bool $accountIsInactive
 * @property string $accountType
 * @property string $createdAt
 * @property string $modifiedAt
 * @property bool $hasPortalUser
 * @property ?string $mauticId
 * @property int $mauticPoint
 * @property string[] $sitesweb
 * @property ?string $middleName
 * @property bool $emailAddressIsOptedOut
 * @property bool $emailAddressIsInvalid
 * @property bool $phoneNumberIsOptedOut
 * @property bool $phoneNumberIsInvalid
 * @property string $accountId
 * @property string $accountName
 * @property ?string $campaignId
 * @property ?string $campaignName
 * @property string $createdById
 * @property string $createdByName
 * @property ?string $modifiedById
 * @property ?string $modifiedByName
 * @property string $assignedUserId
 * @property string $assignedUserName
 * @property ?string $portalUserId
 * @property ?string $portalUserName
 * @property ?string $originalLeadId
 * @property ?string $originalLeadName
 */
class Contact
{
    /**
     * @param stdClass $data
     */
    public function __construct(
        private readonly stdClass $data
    ) {}

    public function __get(string $name): mixed
    {
        return $this->data->$name ?? null;
    }

    /**
     * @param string $name
     * @param array<string, mixed> $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        return $this->data->$name ?? null;
    }
}
