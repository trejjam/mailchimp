<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

/**
 * @property Link[]&Entries $_links
 */
trait LinkTrait
{
    /**
     * @return Link[]
     */
    public function getLinks() : array
    {
        return $this->_links->toArray();
    }
}
