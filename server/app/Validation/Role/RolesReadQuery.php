<?php declare (strict_types=1);

namespace App\Validation\Role;

use App\Json\Schemas\RoleSchema as Schema;
use App\Json\Schemas\UserSchema;
use App\Validation\Role\RoleRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Limoncello\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class RolesReadQuery implements JsonApiQueryRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getIdentityRule(): ?RuleInterface
    {
        return r::asSanitizedString();
    }

    /**
     * @inheritdoc
     */
    public static function getFilterRules(): ?array
    {
        return [
            Schema::RESOURCE_ID                                  => static::getIdentityRule(),
            Schema::ATTR_UUID                                    => r::asSanitizedString(),
            Schema::ATTR_NAME                                    => r::asSanitizedString(),
            Schema::ATTR_DESCRIPTION                             => r::asSanitizedString(),
            Schema::ATTR_CREATED_AT                              => r::asJsonApiDateTime(),
            Schema::ATTR_UPDATED_AT                              => r::asJsonApiDateTime(),
            Schema::REL_USERS                                    => r::asSanitizedString(),
            Schema::REL_USERS . '.' . UserSchema::ATTR_UUID      => r::asSanitizedString(),
            Schema::REL_USERS . '.' . UserSchema::ATTR_EMAIL     => r::asSanitizedString(),
            Schema::REL_USERS . '.' . UserSchema::ATTR_LAST_NAME => r::asSanitizedString(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getFieldSetRules(): ?array
    {
        return [
            Schema::TYPE     => r::inValues([
                Schema::RESOURCE_ID,
                Schema::ATTR_UUID,
                Schema::ATTR_NAME,
                Schema::ATTR_DESCRIPTION,
                Schema::ATTR_CREATED_AT,
                Schema::ATTR_UPDATED_AT,
                Schema::REL_USERS,
            ]),
            UserSchema::TYPE => r::success(),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getSortsRule(): ?RuleInterface
    {
        return r::isString(r::inValues([
            Schema::RESOURCE_ID,
            Schema::ATTR_NAME,
            Schema::ATTR_DESCRIPTION,
            Schema::ATTR_CREATED_AT,
            Schema::ATTR_UPDATED_AT,
            Schema::REL_USERS,
            Schema::REL_USERS . '.' . UserSchema::ATTR_EMAIL,
            Schema::REL_USERS . '.' . UserSchema::ATTR_LAST_NAME,
            Schema::REL_USERS . '.' . UserSchema::ATTR_FIRST_NAME,
        ]));
    }

    /**
     * @inheritdoc
     */
    public static function getIncludesRule(): ?RuleInterface
    {
        return r::isString(r::inValues([
            Schema::REL_USERS,
        ]));
    }

    /**
     * @inheritdoc
     */
    public static function getPageOffsetRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageOffsetRule();
    }

    /**
     * @inheritdoc
     */
    public static function getPageLimitRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageLimitRuleForDefaultAndMaxSizes(
            ApplicationApi::DEFAULT_PAGE_SIZE,
            ApplicationApi::DEFAULT_MAX_PAGE_SIZE
        );
    }
}
