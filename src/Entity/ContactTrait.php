<?php

namespace Trejjam\MailChimp\Entity;

use Nette;
use Schematic;
use Trejjam;

/**
 * @property Contact $contact
 */
trait ContactTrait
{
    /**
     * @return Contact|Schematic\Entry
     */
    public function getContact() : Contact
    {
        return $this->contact;
    }
}
