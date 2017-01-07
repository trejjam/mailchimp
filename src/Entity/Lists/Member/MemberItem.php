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
 * @property-read        $merge_fields
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
class MemberItem extends Schematic\Entry
{
	use Entity\LinkTrait;

	protected static $associations = [
		'_links[]' => Entity\Link::class,
	];
}
