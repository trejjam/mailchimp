<?php

namespace Trejjam\MailChimp\Entity;

use Nette;
use Schematic;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
 * Class Root
 *
 * @package Trejjam\MailChimp\Endpoint
 *
 * @property-read string $account_id
 * @property-read string $account_name
 * @property-read string $email
 * @property-read string $username
 * @property-read string $role
 * @property-read string $pro_enabled
 * @property-read string $last_login
 * @property-read string $total_subscribers
 * @property-read        $industry_stats
 */
class Root extends Schematic\Entry
{
	use Entity\LinkTrait;
	use Entity\ContactTrait;

	protected static $associations = [
		'_links[]' => Entity\Link::class,
		'contact'  => Entity\Contact::class,
	];
}
