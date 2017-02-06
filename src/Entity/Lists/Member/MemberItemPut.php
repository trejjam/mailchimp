<?php

namespace Trejjam\MailChimp\Entity\Lists\Member;

use Nette;
use Trejjam;

class MemberItemPut extends MemberItem
{
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
	 */
	private static function getSubscriberHash($email)
	{
		if ( !Nette\Utils\Validators::isEmail($email)) {
			throw new Trejjam\MailChimp\Exception\CoruptedEmailException();
		}

		return md5(Nette\Utils\Strings::lower($email));
	}

	public function toArray()
	{
		return $this->data;
	}
}
