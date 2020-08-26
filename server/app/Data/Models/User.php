<?php declare (strict_types=1);

namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\DateTimeType;
use Lolltec\Limoncello\Flute\Types\UuidType;

/**
 * @package App
 */
class User implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'users';

    /** Primary key */
    const FIELD_ID = 'id_user';

    /** Foreign key */
    const FIELD_ID_ROLE = Role::FIELD_ID;

    /** Field name */
    const FIELD_EMAIL = 'email';

    /** Field name */
    const FIELD_FIRST_NAME = 'first_name';

    /** Field name */
    const FIELD_LAST_NAME = 'last_name';

    /** Field name */
    const FIELD_PASSWORD_HASH = 'password_hash';

    /** Relationship name */
    const REL_ROLE = 'role';

    /** Minimum email length */
    const MIN_EMAIL_LENGTH = 3;

    /** Minimum password length */
    const MIN_PASSWORD_LENGTH = 8;

    /**
     * @inheritdoc
     */
    public static function getTableName(): string
    {
        return static::TABLE_NAME;
    }

    /**
     * @inheritdoc
     */
    public static function getPrimaryKeyName(): string
    {
        return static::FIELD_ID;
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeTypes(): array
    {
        return [
            self::FIELD_ID            => Type::INTEGER,
            self::FIELD_ID_ROLE       => Role::getAttributeTypes()[Role::FIELD_ID],
            self::FIELD_UUID          => UuidType::NAME,
            self::FIELD_EMAIL         => Type::STRING,
            self::FIELD_FIRST_NAME    => Type::STRING,
            self::FIELD_LAST_NAME     => Type::STRING,
            self::FIELD_PASSWORD_HASH => Type::STRING,
            self::FIELD_CREATED_AT    => DateTimeType::NAME,
            self::FIELD_UPDATED_AT    => DateTimeType::NAME,
            self::FIELD_DELETED_AT    => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_UUID          => 36,
            self::FIELD_EMAIL         => 255,
            self::FIELD_FIRST_NAME    => 100,
            self::FIELD_LAST_NAME     => 100,
            self::FIELD_PASSWORD_HASH => 100,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getRawAttributes(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getRelationships(): array
    {
        return [
            RelationshipTypes::BELONGS_TO => [
                self::REL_ROLE => [Role::class, self::FIELD_ID_ROLE, Role::REL_USERS],
            ],
        ];
    }
}
