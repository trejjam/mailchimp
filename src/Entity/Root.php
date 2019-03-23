<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

use Schematic;

/**
 * @property-read string $account_id
 * @property-read string $account_name
 * @property-read string $email
 * @property-read string $username
 * @property-read string $role
 * @property-read string $pro_enabled
 * @property-read string $last_login
 * @property-read string $total_subscribers
 * @property-read mixed $industry_stats
 */
final class Root extends Schematic\Entry
{
    use LinkTrait;
    use ContactTrait;

    protected static $associations = [
        '_links[]' => Link::class,
        'contact'  => Contact::class,
    ];
}
