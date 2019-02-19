<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

final class Lists
{
    /**
     * @var string[]
     */
    private $lists;

    public function __construct(array $lists)
    {
        $this->lists = $lists;
    }

    public function getListByName(string $name) : string
    {
        return $this->lists[$name];
    }
}
