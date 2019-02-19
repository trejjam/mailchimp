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
     * @return Link[]|Schematic\Entries
     */
    public function getLinks() : Schematic\Entries
    {
        return $this->_links;
    }
}
