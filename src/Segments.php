<?php

namespace Trejjam\MailChimp;

final class Segments
{
    /**
     * @var string[]
     */
    private $segments;

    public function __construct(array $segments)
    {
        $this->segments = $segments;
    }

    public function getSegmentInList(string $listName, string $segmentName) : string
    {
        return $this->segments[$listName][$segmentName];
    }
}
