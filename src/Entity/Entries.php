<?php

namespace Trejjam\MailChimp\Entity;

use Schematic;

class Entries extends Schematic\Entries
{
	public function toArray()
	{
		$out = parent::toArray();

		foreach ($out as $key => $entity) {
			if ($entity instanceof AEntity) {
				$out[$key] = $entity->toArray();
			}
			else if ($entity instanceof Entries) {
				$out[$key] = $entity->toArray();
			}
		}

		return $out;
	}
}
