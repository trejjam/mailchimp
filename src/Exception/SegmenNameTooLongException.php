<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Exception;

final class SegmenNameTooLongException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    private $segmentName;

    public function __construct(string $segmentName)
    {
        $this->segmentName = $segmentName;
    }
}
