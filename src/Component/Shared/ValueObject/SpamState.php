<?php

/**
 * @author Julien Mercier-Rojas <julien@jeckel-lab.fr>
 * Created at: 09/03/2024 19:30
 */
declare(strict_types=1);

namespace App\Component\Shared\ValueObject;

/**
 * @see https://services.renater.fr/antispam/refdocs/marquages
 */
enum SpamState: string
{
    case DCE = 'DCE';   // Dirty Commercial Emails
    case FORUM_EMAIL = 'FORUM-EMAIL';
    case ALERTING = 'ALERTING';
    case PURCHASE = 'PURCHASE';
    case SPAM = 'SPAM';
    case PCE = 'PCE';   // Professional Commercial Emails
    case SCAM = 'SCAM';
    case SOCIAL = 'SOCIAL';
    case MCE = 'MCE';   // Miscellaneous Commercial Emails
    case FINANCE = 'FINANCE';
    case PHISHING = 'PHISHING';
    case ACCOUNT = 'ACCOUNT';
    case OK = 'OK';
    case TRAVEL = 'TRAVEL';
    case SUSPECT = 'SUSPECT';
}
