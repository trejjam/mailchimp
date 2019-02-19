<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity\Lists\Segment;

use Schematic;
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
     * @return Segment[]
     */
    public function getSegments() : array
    {
        return $this->segments->toArray();
    }
}
