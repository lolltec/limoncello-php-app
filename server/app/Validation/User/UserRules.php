<?php declare (strict_types=1);

namespace App\Validation\User;

use App\Data\Models\User as Model;
use App\Json\Schemas\UserSchema as Schema;
use App\Validation\BaseRules;
use App\Validation\ErrorCodes;
use App\Validation\L10n\Messages;
use Limoncello\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class UserRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function userType(): RuleInterface
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
     * @return RuleInterface
     */
    public static function firstName(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_FIRST_NAME];

        return self::asSanitizedString(self::stringLengthMax($maxLength));
    }

    /**
     * @return RuleInterface
     */
    public static function lastName(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_LAST_NAME];

        return self::asSanitizedString(self::stringLengthMax($maxLength));
    }

    /**
     * @param bool|null $onUpdate
     *
     * @return RuleInterface
     */
    public static function email(bool $onUpdate = false): RuleInterface
    {
        $isUnique  = self::unique(Model::TABLE_NAME, Model::FIELD_EMAIL, $onUpdate === false ? null : Model::FIELD_ID);
        $likeEmail = self::filter(
            FILTER_VALIDATE_EMAIL,
            null,
            ErrorCodes::IS_EMAIL,
            Messages::IS_EMAIL,
            $isUnique
        );
        $maxLength = Model::getAttributeLengths()[Model::FIELD_EMAIL];

        return self::isString(self::stringLengthBetween(Model::MIN_EMAIL_LENGTH, $maxLength, $likeEmail));
    }

    /**
     * @return RuleInterface
     */
    public static function password(): RuleInterface
    {
        return self::isString(self::stringLengthMin(Model::MIN_PASSWORD_LENGTH));
    }
}
