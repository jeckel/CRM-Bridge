<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 08/02/2024
 */

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Component\Shared\Identity\CompanyId;
use App\Infrastructure\Doctrine\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Override;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

use function App\slug;

/**
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company implements SlugAwareInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'company_id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: Types::STRING, length: 180, nullable: false)]
    private string $name = '';

    #[ORM\Column(type: Types::STRING, length: 180, unique: true, nullable: false)]
    private string $slug = '';

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true)]
    private ?string $espoCompanyId = null;

    /**
     * @var Collection<int, Contact> $contacts
     */
    #[ORM\OneToMany(
        mappedBy: 'company',
        targetEntity: Contact::class,
        cascade: ['persist']
    )]
    private Collection $contacts;

    private CompanyId $companyId;

    public function __construct()
    {
        $this->contacts = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getIdentity(): CompanyId
    {
        if (! isset($this->companyId)) {
            $this->companyId = CompanyId::from($this->id->toString());
        }
        return $this->companyId;
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Company
    {
        $this->name = $name;
        if (!isset($this->slug)) {
            $this->setSlug(slug($name));
        }
        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    #[Override]
    public function setSlug(string $slug): Company
    {
        $this->slug = $slug;
        return $this;
    }

    #[Override]
    public function getSlugSource(): string
    {
        return $this->name;
    }

    public function getEspoCompanyId(): ?string
    {
        return $this->espoCompanyId;
    }

    public function setEspoCompanyId(?string $espoCompanyId): Company
    {
        $this->espoCompanyId = $espoCompanyId;
        return $this;
    }

    /**
     * @return Collection<int, Contact>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    public function addContact(Contact $contact): static
    {
        if (!$this->contacts->contains($contact)) {
            $this->contacts->add($contact);
            $contact->setCompany($this);
        }

        return $this;
    }

    public function removeContact(Contact $contact): static
    {
        if ($this->contacts->removeElement($contact)) {
            // set the owning side to null (unless already changed)
            if ($contact->getCompany() === $this) {
                $contact->setCompany(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function hasId(): bool
    {
        return isset($this->id);
    }
}
