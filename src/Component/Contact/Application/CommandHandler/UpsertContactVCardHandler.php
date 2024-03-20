<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 18/03/2024
 */

declare(strict_types=1);

namespace App\Component\Contact\Application\CommandHandler;

use App\Component\Contact\Application\Command\UpsertContactVCard;
use App\Component\Contact\Application\Port\RepositoryPort;
use App\Component\Contact\Domain\Entity\Company;
use App\Component\Contact\Domain\Entity\Contact;
use JeckelLab\Contract\Infrastructure\System\Clock;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use function App\slug;

#[AsMessageHandler]
readonly class UpsertContactVCardHandler
{
    public function __construct(
        private RepositoryPort $repository,
        private Clock $clock
    ) {}

    public function __invoke(UpsertContactVCard $command): void
    {
        $contact = $this->repository->findByVCardUri($command->vCard->vCardUri);
        $company = null;
        if (null !== ($companyName = $command->vCard->company())) {
            $company = $this->repository
                ->findCompanyBySlug(slug($companyName)) ??
                Company::new($companyName)
            ;
        }
        if (null !== $contact) {
            $contact->update(
                displayName: $command->vCard->displayName(),
                vCardEtag: $command->vCardEtag,
                vCardLastSyncAt: $this->clock->now(),
                company: $company
            );
        }
        if (null === $contact) {
            $contact = Contact::new(
                displayName: $command->vCard->displayName(),
                vCardUri: $command->vCard->vCardUri,
                vCardEtag: $command->vCardEtag,
                vCardLastSyncAt: $this->clock->now(),
                addressBook: $this->repository->getAddressBookReference($command->addressBookId),
                company: $company
            );
        }
        $this->repository->persist($contact);
        $this->repository->flush();
    }
}
