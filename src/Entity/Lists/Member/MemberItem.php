<?php

namespace Trejjam\MailChimp\Entity\Lists\Member;

use Nette;
use Schematic;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
 * Class MemberItem
 *
 * @package Trejjam\MailChimp\Entity\Lists\Member
 *
 * @property-read string $id
 * @property-read string $email_address
 * @property-read string $unique_email_id
 * @property-read string $email_type
 * @property-read string $status
 * @property-read array  $merge_fields
 * @property-read        $stats
 * @property-read string $ip_signup
 * @property-read string $timestamp_signup
 * @property-read string $ip_opt
 * @property-read string $timestamp_opt
 * @property-read int    $member_rating
 * @property-read string $last_changed
 * @property-read string $language
 * @property-read bool   $vip
 * @property-read string $email_client
 * @property-read        $location
 * @property-read string $list_id
 */
class MemberItem
{
	use Entity\LinkTrait;

	const STATUS_SUBSCRIBED    = 'subscribed';
	const STATUS_UNSUBSCRIBED  = 'unsubscribed';
	const STATUS_CLEANED       = 'cleaned';
	const STATUS_PENDING       = 'pending';
	const STATUS_TRANSACTIONAL = 'transactional';

	const MERGE_FIELDS_FNAME = 'FNAME';
	const MERGE_FIELDS_LNAME = 'LNAME';

	protected $initializedData = [];

	protected $associations = [
		'_links' => [Entity\Link::class],
	];

	/**
	 * @var array
	 */
	protected $data;

	public function __construct(array $data)
	{
		$this->data = $data;
	}

	public function __get($name)
	{
		if (isset($this->associations[$name])) {
			if (is_array($this->associations[$name])) {
				$this->initializedData[$name] = new Schematic\Entries($this->data[$name], $this->associations[$name][0]);
			}
			else {
				$this->initializedData[$name] = new $this->associations[$name]($this->data[$name]);
			}
		}
		else {
			$this->initializedData[$name] = $this->data[$name];
		}

		return $this->initializedData[$name];
	}

	/**
	 * @param 'html'|'text' $emailType
	 */
	public function setEmailType($emailType)
	{
		$this->set('email_type', $emailType);
	}

	public function setMergeFields(array $mergeFields)
	{
		$this->set('merge_fields', $mergeFields);
	}

	protected function set($name, $value)
	{
		$this->data[$name] = $value;
		$this->initializedData[$name] = $value;
	}
}
