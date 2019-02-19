<?php

namespace Trejjam\MailChimp\Entity\Lists\Segment;

use Schematic;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
 * @property-read Segment[] $segments
 * @property-read string    $list_id
 * @property-read int       $total_items
 */
final class Lists extends Schematic\Entry
{
    use Entity\LinkTrait;

    protected static $associations = [
        '_links[]'   => Entity\Link::class,
        'segments[]' => Segment::class,
    ];

    /**
     * @return Segment[]|Schematic\Entries
     */
    public function getSegments() : Schematic\Entries
    {
        return $this->segments;
    }
}
