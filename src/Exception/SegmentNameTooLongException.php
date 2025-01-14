<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

final class SegmentNameTooLongException extends \InvalidArgumentException
{
    private string $segmentName;

    public function __construct(string $segmentName)
    {
        $this->segmentName = $segmentName;
        parent::__construct();
    }

    public function getSegmentName() : string
    {
        return $this->segmentName;
    }
}
