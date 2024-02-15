<?php

/**
 * @author: Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 12/02/2024
 */

declare(strict_types=1);

namespace App\Presentation\Voter;

use App\Infrastructure\Doctrine\Entity\AccountAwareInterface;
use App\Infrastructure\Doctrine\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Security\Permission;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, object>
 */
class EntityVoter extends Voter
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    /** @phpstan-ignore-next-line */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [Permission::EA_ACCESS_ENTITY, Permission::EA_EXECUTE_ACTION], true)) {
            return false;
        }
        if ($subject instanceof EntityDto && is_a($subject->getInstance(), AccountAwareInterface::class)) {
            return true;
        }
        return false;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    #[Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        if ($user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }
        if (! $subject instanceof EntityDto) {
            return false;
        }
        $entity = $subject->getInstance();
        if (!is_a($entity, AccountAwareInterface::class)) {
            return true;
        }
        return $entity->getAccountOrFail()->getId() === $user->getAccountOrFail()->getId();
    }
}
