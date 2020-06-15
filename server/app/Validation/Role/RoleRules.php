<?php declare (strict_types=1);

namespace App\Validation\Role;

use App\Data\Models\Role as Model;
use App\Json\Schemas\RoleSchema as Schema;
use App\Validation\BaseRules;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class RoleRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function roleType(): RuleInterface
    {
        return self::equals(Schema::TYPE);
    }

    /**
     * @param bool $onUpdate
     *
     * @return RuleInterface
     */
    public static function uuid(bool $onUpdate = false): RuleInterface
    {
        $isUnique  = self::unique(Model::TABLE_NAME, Model::FIELD_UUID, $onUpdate === false ? null : Model::FIELD_ID);
        $maxLength = Model::getAttributeLengths()[Model::FIELD_UUID];

        return self::isUuid(self::stringLengthMax($maxLength, $isUnique));
    }

    /**
     * @param bool $onUpdate
     *
     * @return RuleInterface
     */
    public static function name(bool $onUpdate = false): RuleInterface
    {
        $isUnique  = self::unique(Model::TABLE_NAME, Model::FIELD_NAME, $onUpdate === false ? null : Model::FIELD_ID);
        $maxLength = Model::getAttributeLengths()[Model::FIELD_NAME];

        return self::isString(self::stringLengthBetween(Model::MIN_NAME_LENGTH, $maxLength, $isUnique));
    }
}
