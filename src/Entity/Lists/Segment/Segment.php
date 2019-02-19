<?php

namespace Trejjam\MailChimp\Entity\Lists\Segment;

use Schematic;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
 * @property-read int    $id
 * @property-read string $name
 * @property-read int    $member_count
 * @property-read string $type
 * @property-read string $created_at
 * @property-read string $updated_at
 * @property-read        $options
 * @property-read string $list_id
 */
final class Segment extends Schematic\Entry
{
	use Entity\LinkTrait;

	protected static $associations = [
		'_links[]' => Entity\Link::class,
	];
}
