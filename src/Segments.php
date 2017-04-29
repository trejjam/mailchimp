<?php

namespace Trejjam\MailChimp;

class Segments
{
	/**
	 * @var string[]
	 */
	private $segments;

	public function __construct(array $segments)
	{
		$this->segments = $segments;
	}

	public function getSegmentInList($listName, $segmentName)
	{
		return $this->segments[$listName][$segmentName];
	}
}
