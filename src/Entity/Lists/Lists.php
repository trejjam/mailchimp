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
 * @property-read ListItem[] $lists
 * @property-read int        $total_items
 */
class Lists extends Schematic\Entry
{
	use Entity\LinkTrait;

	protected static $associations = [
		'_links[]' => Entity\Link::class,
		'lists[]'  => ListItem::class,
	];

	/**
	 * @return ListItem[]|Schematic\Entries
	 */
	public function getLists()
	{
		return $this->lists;
	}
}
