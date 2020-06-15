<?php declare (strict_types=1);

namespace App\Data\Models;

use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Application\ModelInterface;
use Limoncello\Contracts\Data\RelationshipTypes;
use Limoncello\Flute\Types\DateTimeType;
use Limoncello\Flute\Types\UuidType;

/**
 * @package App
 */
class Role implements ModelInterface, CommonFields
{
    /** Table name */
    const TABLE_NAME = 'roles';

    /** Primary key */
    const FIELD_ID = 'id_role';

    /** Field name */
    const FIELD_NAME = 'name';

    /** Field name */
    const FIELD_DESCRIPTION = 'description';

    /** Relationship name */
    const REL_USERS = 'users';

    /** Minimum id length */
    const MIN_NAME_LENGTH = 2;

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
            self::FIELD_ID          => Type::INTEGER,
            self::FIELD_UUID        => UuidType::NAME,
            self::FIELD_NAME        => Type::STRING,
            self::FIELD_DESCRIPTION => Type::TEXT,
            self::FIELD_CREATED_AT  => DateTimeType::NAME,
            self::FIELD_UPDATED_AT  => DateTimeType::NAME,
            self::FIELD_DELETED_AT  => DateTimeType::NAME,
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeLengths(): array
    {
        return [
            self::FIELD_UUID => 36,
            self::FIELD_NAME => 255,
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
            RelationshipTypes::HAS_MANY => [
                self::REL_USERS => [User::class, User::FIELD_ID_ROLE, User::REL_ROLE],
            ],
        ];
    }
}
