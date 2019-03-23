<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

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
