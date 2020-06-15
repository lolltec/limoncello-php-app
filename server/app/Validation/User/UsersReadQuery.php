<?php declare (strict_types=1);

namespace App\Validation\User;

use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema as Schema;
use App\Validation\User\UserRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiQueryRulesInterface;
use Limoncello\Flute\Validation\JsonApi\Rules\DefaultQueryValidationRules;
use Limoncello\Validation\Contracts\Rules\RuleInterface;
use Settings\ApplicationApi;

/**
 * @package App
 */
class UsersReadQuery implements JsonApiQueryRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getIdentityRule(): ?RuleInterface
    {
        return r::asSanitizedString();
    }

    /**
     * @return RuleInterface[]|null
     */
    public static function getFilterRules(): ?array
    {
        return [
            Schema::RESOURCE_ID                                   => static::getIdentityRule(),
            Schema::ATTR_UUID                                     => r::asSanitizedString(),
            Schema::ATTR_EMAIL                                    => r::asSanitizedString(),
            Schema::ATTR_FIRST_NAME                               => r::asSanitizedString(),
            Schema::ATTR_LAST_NAME                                => r::asSanitizedString(),
            Schema::ATTR_CREATED_AT                               => r::asJsonApiDateTime(),
            Schema::ATTR_UPDATED_AT                               => r::asSanitizedString(),
            Schema::REL_ROLE                                      => r::asSanitizedString(),
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_UUID        => r::asSanitizedString(),
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_NAME        => r::asSanitizedString(),
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_DESCRIPTION => r::asSanitizedString(),
        ];
    }

    /**
     * @return RuleInterface[]|null
     */
    public static function getFieldSetRules(): ?array
    {
        return [
            // if fields sets are given only the following fields are OK
            Schema::TYPE     => r::inValues([
                Schema::RESOURCE_ID,
                Schema::ATTR_FIRST_NAME,
                Schema::ATTR_LAST_NAME,
                Schema::ATTR_CREATED_AT,
                Schema::ATTR_UPDATED_AT,
                Schema::REL_ROLE,
            ]),
            // roles field sets could be any
            RoleSchema::TYPE => r::success(),
        ];
    }

    /**
     * @return RuleInterface|null
     */
    public static function getSortsRule(): ?RuleInterface
    {
        return r::isString(r::inValues([
            Schema::RESOURCE_ID,
            Schema::ATTR_FIRST_NAME,
            Schema::ATTR_LAST_NAME,
            Schema::ATTR_CREATED_AT,
            Schema::ATTR_UPDATED_AT,
            Schema::REL_ROLE,
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_UUID,
            Schema::REL_ROLE . '.' . RoleSchema::ATTR_NAME,
        ]));
    }

    /**
     * @return RuleInterface|null
     */
    public static function getIncludesRule(): ?RuleInterface
    {
        return r::isString(r::inValues([
            Schema::REL_ROLE,
        ]));
    }

    /**
     * @return RuleInterface|null
     */
    public static function getPageOffsetRule(): ?RuleInterface
    {
        // defaults are fine
        return DefaultQueryValidationRules::getPageOffsetRule();
    }

    /**
     * @return RuleInterface|null
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
