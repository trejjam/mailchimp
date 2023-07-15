<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

final class SegmentNameTooLongException extends \InvalidArgumentException
{
    public function __construct(private readonly string $segmentName)
    {
        parent::__construct();
    }

    public function getSegmentName() : string
    {
        return $this->segmentName;
    }
}
