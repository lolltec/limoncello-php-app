<?php declare (strict_types=1);

namespace App\Validation\User;

use App\Json\Schemas\UserSchema as Schema;
use App\Validation\User\UserRules as r;
use Limoncello\Flute\Contracts\Validation\JsonApiDataRulesInterface;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class UserCreateJson implements JsonApiDataRulesInterface
{
    /**
     * @inheritdoc
     */
    public static function getTypeRule(): RuleInterface
    {
        return r::userType();
    }

    /**
     * @inheritdoc
     */
    public static function getIdRule(): RuleInterface
    {
        return r::equals(null);
    }

    /**
     * @inheritdoc
     */
    public static function getAttributeRules(): array
    {
        return [
            Schema::ATTR_EMAIL      => r::required(r::email()),
            Schema::ATTR_FIRST_NAME => r::nullable(r::firstName()),
            Schema::ATTR_LAST_NAME  => r::nullable(r::lastName()),
            Schema::V_ATTR_PASSWORD => r::required(r::password()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToOneRelationshipRules(): array
    {
        return [
            Schema::REL_ROLE => r::required(r::roleRelationship()),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function getToManyRelationshipRules(): array
    {
        return [];
    }
}
