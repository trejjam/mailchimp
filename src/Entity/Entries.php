<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity;

use Schematic;

final class Entries extends Schematic\Entries
{
	/**
	 * @return Schematic\Entry[]
	 */
	public function toArray() : array
	{
		$out = parent::toArray();

		/** @var AEntity|Entries|Schematic\Entry $entity */
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
