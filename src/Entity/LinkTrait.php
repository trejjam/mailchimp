<?php

namespace Trejjam\MailChimp\Entity;

use Nette;
use Schematic;
use Trejjam;

/**
 * @property Link[] $_links
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
