<?php
declare(strict_types=1);

namespace Trejjam\MailChimp\Entity\Lists\Member;

use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Trejjam\MailChimp\Entity;
use Trejjam\MailChimp\Exception\CoruptedEmailException;

/**
 * @property-read string $id
 * @property string $email_address
 * @property-read string $unique_email_id
 * @property string $email_type
 * @property-read string $status
 * @property array $merge_fields
 * @property-read mixed $stats
 * @property-read string $ip_signup
 * @property-read string $timestamp_signup
 * @property-read string $ip_opt
 * @property-read string $timestamp_opt
 * @property-read int $member_rating
 * @property-read string $last_changed
 * @property-read string $language
 * @property-read bool $vip
 * @property-read string $email_client
 * @property-read mixed $location
 * @property-read string $list_id
 */
final class MemberItem extends Entity\AEntity
{
    use Entity\LinkTrait;

    public const STATUS_SUBSCRIBED = 'subscribed';
    public const STATUS_UNSUBSCRIBED = 'unsubscribed';
    public const STATUS_CLEANED = 'cleaned';
    public const STATUS_PENDING = 'pending';
    public const STATUS_TRANSACTIONAL = 'transactional';

    public const MERGE_FIELDS_FNAME = 'FNAME';
    public const MERGE_FIELDS_LNAME = 'LNAME';

    protected $readOnly = [
        'unique_email_id' => true,
        'stats' => true,
        'ip_signup' => true,
        'timestamp_signup' => true,
        'ip_opt' => true,
        'timestamp_opt' => true,
        'member_rating' => true,
        'last_changed' => true,
        'email_client' => true,
        'last_note' => true,
        'list_id' => true,
        '_links' => true,
    ];

    protected $associations = [
        '_links' => [Entity\Link::class],
    ];

    public function setEmailAddress(string $emailAddress) : self
    {
        $this->email_address = $emailAddress;

        return $this;
    }

    public function setEmailType(string $emailType) : void
    {
        if (!in_array($emailType, ['html' | 'text'], true)) {
            throw new \InvalidArgumentException();
        }

        $this->email_type = $emailType;
    }

    public function setMergeFields(array $mergeFields) : void
    {
        $this->merge_fields = $mergeFields;
    }

    public static function create(string $email, string $listId, ?string $status = null) : self
    {
        $data = [
            'id' => self::getSubscriberHash($email),
            'email_address' => $email,
            'list_id' => $listId,
        ];

        if (in_array($status, [
            self::STATUS_SUBSCRIBED,
            self::STATUS_UNSUBSCRIBED,
            self::STATUS_CLEANED,
            self::STATUS_PENDING,
            self::STATUS_TRANSACTIONAL,
        ], true)) {
            $data['status_if_new'] = $status;
        }

        return new self($data);
    }

    /**
     * @throws CoruptedEmailException
     */
    public static function getSubscriberHash(string $email) : string
    {
        if (!Validators::isEmail($email)) {
            throw new CoruptedEmailException();
        }

        return md5(Strings::lower($email));
    }
}
