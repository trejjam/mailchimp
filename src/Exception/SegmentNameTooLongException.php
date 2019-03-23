<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

final class SegmentNameTooLongException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    private $segmentName;

    public function __construct(string $segmentName)
    {
    	parent::__construct();

        $this->segmentName = $segmentName;
    }
}
