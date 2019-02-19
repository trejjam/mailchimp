<?php
declare(strict_types=1);

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
final class MemberItem extends Entity\AEntity
{
    use Entity\LinkTrait;

    const STATUS_SUBSCRIBED = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_CLEANED = 'cleaned';
    const STATUS_PENDING = 'pending';
    const STATUS_TRANSACTIONAL = 'transactional';

    const MERGE_FIELDS_FNAME = 'FNAME';
    const MERGE_FIELDS_LNAME = 'LNAME';

    protected $readOnly = [
        'unique_email_id'  => true,
        'stats'            => true,
        'ip_signup'        => true,
        'timestamp_signup' => true,
        'ip_opt'           => true,
        'timestamp_opt'    => true,
        'member_rating'    => true,
        'last_changed'     => true,
        'email_client'     => true,
        'last_note'        => true,
        'list_id'          => true,
        '_links'           => true,
    ];

    protected $associations = [
        '_links' => [Entity\Link::class],
    ];

    public function setEmailAddress(string $emailAddress) : self
    {
        $this->email_address = $emailAddress;

        return $this;
    }

    /**
     * @param 'html'|'text' $emailType
     */
    public function setEmailType(string $emailType) : void
    {
        $this->email_type = $emailType;
    }

    public function setMergeFields(array $mergeFields) : void
    {
        $this->merge_fields = $mergeFields;
    }

    public static function create(string $email, string $listId, ?string $status = null) : self
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
     * @throws Trejjam\MailChimp\Exception\CoruptedEmailException
     */
    public static function getSubscriberHash(string $email) : string
    {
        if (!Nette\Utils\Validators::isEmail($email)) {
            throw new Trejjam\MailChimp\Exception\CoruptedEmailException;
        }

        return md5(Nette\Utils\Strings::lower($email));
    }
}
