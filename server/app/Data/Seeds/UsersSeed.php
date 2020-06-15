<?php declare (strict_types=1);

namespace App\Data\Seeds;

use App\Data\Models\User as Model;
use Doctrine\DBAL\DBALException;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Crypt\Contracts\HasherInterface;
use Limoncello\Data\Seeds\SeedTrait;
use Ramsey\Uuid\UuidFactoryInterface;

/**
 * @package App
 */
class UsersSeed implements SeedInterface
{
    use SeedTrait;

    /** Default password */
    const DEFAULT_PASSWORD = 'default_secret';

    /** User id */
    const ID_ADMINISTRATOR = 1;
    /** User email */
    const USER_ADMINISTRATOR = 'administrator@local.ltd';

    /** User id */
    const ID_MODERATOR = 2;
    /** User email */
    const USER_MODERATOR = 'moderator@local.ltd';

    /** User id */
    const ID_USER = 3;
    /** User email */
    const USER_USER = 'user@local.ltd';

    /**
     * @inheritdoc
     *
     * @throws DBALException
     */
    public function run(): void
    {
        /** @var HasherInterface $uuid */
        /** @var UuidFactoryInterface $uuid */
        $uuid   = $this->getContainer()->get(UuidFactoryInterface::class);
        $hasher = $this->getContainer()->get(HasherInterface::class);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID            => self::ID_ADMINISTRATOR,
            Model::FIELD_ID_ROLE       => RolesSeed::ROLE_ADMINISTRATOR,
            Model::FIELD_UUID          => $uuid->uuid4()->toString(),
            Model::FIELD_EMAIL         => self::USER_ADMINISTRATOR,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
            Model::FIELD_CREATED_AT    => $this->now(),
        ]);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID            => self::ID_MODERATOR,
            Model::FIELD_ID_ROLE       => RolesSeed::ROLE_MODERATOR,
            Model::FIELD_UUID          => $uuid->uuid4()->toString(),
            Model::FIELD_EMAIL         => self::USER_MODERATOR,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
            Model::FIELD_CREATED_AT    => $this->now(),
        ]);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID            => self::ID_USER,
            Model::FIELD_ID_ROLE       => RolesSeed::ROLE_USER,
            Model::FIELD_UUID          => $uuid->uuid4()->toString(),
            Model::FIELD_EMAIL         => self::USER_USER,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
            Model::FIELD_CREATED_AT    => $this->now(),
        ]);
    }
}
