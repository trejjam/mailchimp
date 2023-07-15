<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

final class PaginationOption
{
    public function __construct(
        private int $offset = 0,
        private int $count = 10
    ) {
    }

    public function getOffset() : int
    {
        return $this->offset;
    }

    public function getCount() : int
    {
        return $this->count;
    }

    public function setOffset(int $offset) : self
    {
        $that = clone $this;
        $that->offset = $offset;

        return $that;
    }

    public function setCount(int $count) : self
    {
        $that = clone $this;
        $this->count = $count;

        return $that;
    }

    public function nextPage() : self
    {
        return $this->setOffset(
            $this->getOffset() + $this->getCount()
        );
    }
}
