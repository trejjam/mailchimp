<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

use Schematic;

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
