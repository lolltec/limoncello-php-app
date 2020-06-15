<?php declare (strict_types=1);

namespace App\Authorization;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\UserSchema as Schema;
use Limoncello\Application\Contracts\Authorization\ResourceAuthorizationRulesInterface;
use Limoncello\Auth\Contracts\Authorization\PolicyInformation\ContextInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package App
 */
class UserRules implements ResourceAuthorizationRulesInterface
{
    use RulesTrait;

    /** Action name */
    const ACTION_VIEW_USERS = 'canViewUsers';

    /** Action name */
    const ACTION_CREATE_USER = 'canCreateUser';

    /** Action name */
    const ACTION_EDIT_USER = 'canEditUser';

    /** Action name */
    const ACTION_VIEW_ROLE = 'canViewRole';

    /**
     * @inheritdoc
     */
    public static function getResourcesType(): string
    {
        return Schema::TYPE;
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewUsers(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_VIEW_USERS);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canCreateUser(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_ADMIN_USERS);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canEditUser(ContextInterface $context): bool
    {
        return self::hasScope($context, PassportSeed::SCOPE_ADMIN_USERS);
    }

    /**
     * @param ContextInterface $context
     *
     * @return bool
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function canViewRole(ContextInterface $context): bool
    {
        return self::canViewUsers($context) && RoleRules::canViewRoles($context);
    }
}

