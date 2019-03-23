<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

use Schematic;

/**
 * @property Contact $contact
 */
trait ContactTrait
{
    public function getContact() : Contact
    {
        return $this->contact;
    }
}
