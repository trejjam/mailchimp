<?php

namespace Trejjam\MailChimp\Entity;

use Nette;
use Schematic;
use Trejjam;

/**
 * Class LinksTrait
 *
 * @package Trejjam\MailChimp\Entity
 *
 * @property Contact $contact
 */
trait ContactTrait
{
	/**
	 * @return Contact|Schematic\Entry
	 */
	public function getContact()
	{
		return $this->contact;
	}
}
