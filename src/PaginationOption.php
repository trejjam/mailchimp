<?php
declare(strict_types=1);

namespace Trejjam\MailChimp;

final class PaginationOption
{
    private int $offset;
    private int $count;

    public function __construct(
        int $offset = 0,
        int $count = 10
    ) {
        $this->count = $count;
        $this->offset = $offset;
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
