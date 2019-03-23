<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity\Lists\Member;

use Schematic;
use Schematic\Entries;
use Trejjam\MailChimp\Entity;

/**
 * @property-read MemberItem[]&Entries $members
 * @property-read string               $list_id
 * @property-read int                  $total_items
 */
final class Lists extends Schematic\Entry
{
    use Entity\LinkTrait;

    protected static $associations = [
        '_links[]'  => Entity\Link::class,
        'members[]' => MemberItem::class,
    ];

    /**
     * @return MemberItem[]
     */
    public function getMembers() : array
    {
        return $this->members->toArray();
    }
}
