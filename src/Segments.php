<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

use Trejjam\MailChimp\Exception\SegmentNotFoundException;

final class Segments
{
    /**
     * @var string[][]
     */
    private $segments;

    /**
     * @param string[][] $segments
     */
    public function __construct(
        array $segments
    ) {
        $this->segments = $segments;
    }

    public function getSegmentInList(string $listName, string $segmentName) : string
    {
        if (!array_key_exists($listName, $this->segments)
            || !array_key_exists($segmentName, $this->segments[$listName])
        ) {
            throw new SegmentNotFoundException();
        }

        return $this->segments[$listName][$segmentName];
    }
}
