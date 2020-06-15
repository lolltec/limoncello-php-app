<?php declare (strict_types=1);

namespace App\Json\Schemas;

use App\Data\Models\Role as Model;

/**
 * @package App
 */
class RoleSchema extends BaseSchema
{
    /** Type */
    const TYPE = 'roles';

    /** Model class name */
    const MODEL = Model::class;

    /** Attribute name */
    const ATTR_NAME = Model::FIELD_NAME;

    /** Attribute name */
    const ATTR_DESCRIPTION = Model::FIELD_DESCRIPTION;

    /** Relationship name */
    const REL_USERS = Model::REL_USERS;

    /**
     * @inheritdoc
     */
    public static function getMappings(): array
    {
        return [
            self::SCHEMA_ATTRIBUTES    => [
                self::RESOURCE_ID      => Model::FIELD_ID,
                self::ATTR_UUID        => Model::FIELD_UUID,
                self::ATTR_NAME        => Model::FIELD_NAME,
                self::ATTR_DESCRIPTION => Model::FIELD_DESCRIPTION,
                self::ATTR_CREATED_AT  => Model::FIELD_CREATED_AT,
                self::ATTR_UPDATED_AT  => Model::FIELD_UPDATED_AT,
            ],
            self::SCHEMA_RELATIONSHIPS => [
                self::REL_USERS => Model::REL_USERS,
            ],
        ];
    }
}
