<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

final class Lists
{
    /**
     * @param string[] $lists
     */
    public function __construct(
        private readonly array $lists
    ) {
    }

    public function getListByName(string $name) : string
    {
        return $this->lists[$name];
    }
}
