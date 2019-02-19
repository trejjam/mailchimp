<?php

namespace Trejjam\MailChimp\Entity\Lists;

use Nette;
use Schematic;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
 * @property-read ListItem[] $lists
 * @property-read int        $total_items
 */
final class Lists extends Schematic\Entry
{
    use Entity\LinkTrait;

    protected static $associations = [
        '_links[]' => Entity\Link::class,
        'lists[]'  => ListItem::class,
    ];

    /**
     * @return ListItem[]
     */
    public function getLists() : array
    {
        return $this->lists->toArray();
    }
}
