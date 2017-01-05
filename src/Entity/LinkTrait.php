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
 * @property Link[] $_links
 */
trait LinkTrait
{
	/**
	 * @return Link[]|Schematic\Entries
	 */
	public function getLinks()
	{
		return $this->_links;
	}
}
