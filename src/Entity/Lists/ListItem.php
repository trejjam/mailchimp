<?php

namespace Trejjam\MailChimp\Entity\Lists;

use Nette;
use Schematic;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
 * Class Lists
 *
 * @package Trejjam\MailChimp\Entity\Lists
 *
 * @property-read string $id
 * @property-read string $name
 * @property-read string $permission_reminder
 * @property-read bool   $use_archive_bar
 * @property-read        $campaign_defaults
 * @property-read string $notify_on_subscribe
 * @property-read string $notify_on_unsubscribe
 * @property-read string $date_created
 * @property-read int    $list_rating
 * @property-read bool   $email_type_option
 * @property-read string $subscribe_url_short
 * @property-read string $subscribe_url_long
 * @property-read string $beamer_address
 * @property-read string $visibility
 * @property-read        $modules
 * @property-read        $stats
 */
class ListItem extends Schematic\Entry
{
	use Entity\LinkTrait;
	use Entity\ContactTrait;

	protected static $associations = [
		'_links[]' => Entity\Link::class,
		'contact'  => Entity\Contact::class,
	];
}
