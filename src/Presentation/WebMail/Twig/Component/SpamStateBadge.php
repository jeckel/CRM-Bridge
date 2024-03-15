<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 19:35
 */
declare(strict_types=1);

namespace App\Presentation\WebMail\Twig\Component;

use App\Component\Shared\ValueObject\SpamState;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class SpamStateBadge
{
    public string $spamState;

    public function getColor(): string
    {
        $spamState = SpamState::tryFrom($this->spamState);
        return match($spamState) {
            SpamState::SPAM => 'red',
            SpamState::DCE => 'orange',
            SpamState::FORUM_EMAIL => 'cyan',
            SpamState::ALERTING => 'green',
            SpamState::PURCHASE => 'blue',
            SpamState::PCE => 'teal',
            SpamState::SCAM => 'red',
            SpamState::SOCIAL => 'cyan',
            SpamState::MCE => 'yellow',
            SpamState::FINANCE => 'blue',
            SpamState::PHISHING => 'red',
            SpamState::ACCOUNT => 'blue',
            SpamState::OK => 'green',
            SpamState::TRAVEL => 'blue',
            SpamState::SUSPECT => 'orange',
            default => 'secondary'
        };
    }
}
