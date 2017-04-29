<?php

namespace Trejjam\MailChimp\Entity\Lists\Member;

use Nette;
use Trejjam;
use Trejjam\MailChimp\Entity;

/**
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
class MemberItem extends Entity\AEntity
{
	use Entity\LinkTrait;

	const STATUS_SUBSCRIBED    = 'subscribed';
	const STATUS_UNSUBSCRIBED  = 'unsubscribed';
	const STATUS_CLEANED       = 'cleaned';
	const STATUS_PENDING       = 'pending';
	const STATUS_TRANSACTIONAL = 'transactional';

	const MERGE_FIELDS_FNAME = 'FNAME';
	const MERGE_FIELDS_LNAME = 'LNAME';

	protected $readOnly = [
		'unique_email_id'  => TRUE,
		'stats'            => TRUE,
		'ip_signup'        => TRUE,
		'timestamp_signup' => TRUE,
		'ip_opt'           => TRUE,
		'timestamp_opt'    => TRUE,
		'member_rating'    => TRUE,
		'last_changed'     => TRUE,
		'email_client'     => TRUE,
		'last_note'        => TRUE,
		'list_id'          => TRUE,
		'_links'           => TRUE,
	];

	protected $associations = [
		'_links' => [Entity\Link::class],
	];

	/**
	 * @param string $emailAddress
	 *
	 * @return static
	 */
	public function setEmailAddress($emailAddress)
	{
		$this->email_address = $emailAddress;

		return $this;
	}

	/**
	 * @param 'html'|'text' $emailType
	 */
	public function setEmailType($emailType)
	{
		$this->email_type = $emailType;
	}

	public function setMergeFields(array $mergeFields)
	{
		$this->merge_fields = $mergeFields;
	}

	/**
	 * @param string $email
	 * @param string $listId
	 * @param string $status
	 *
	 * @return static
	 */
	public static function create($email, $listId, $status = NULL)
	{
		$data = [
			'id'            => static::getSubscriberHash($email),
			'email_address' => $email,
			'list_id'       => $listId,
		];

		if (
			!is_null($status)
			&& in_array($status, [
				static::STATUS_SUBSCRIBED,
				static::STATUS_UNSUBSCRIBED,
				static::STATUS_CLEANED,
				static::STATUS_PENDING,
				static::STATUS_TRANSACTIONAL,
			])
		) {
			$data['status_if_new'] = $status;
		}

		return new static($data);
	}

	/**
	 * @param string $email
	 *
	 * @return string
	 * @throws Trejjam\MailChimp\Exception\CoruptedEmailException
	 */
	public static function getSubscriberHash($email)
	{
		if ( !Nette\Utils\Validators::isEmail($email)) {
			throw new Trejjam\MailChimp\Exception\CoruptedEmailException();
		}

		return md5(Nette\Utils\Strings::lower($email));
	}
}
