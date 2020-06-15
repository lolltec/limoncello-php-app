<?php declare (strict_types=1);

namespace App\Data\Seeds;

use App\Data\Models\Role as Model;
use Doctrine\DBAL\DBALException;
use Exception;
use Limoncello\Contracts\Data\SeedInterface;
use Limoncello\Data\Seeds\SeedTrait;
use Ramsey\Uuid\UuidFactoryInterface;

/**
 * @package App
 */
class RolesSeed implements SeedInterface
{
    use SeedTrait;

    /** Role id */
    const ROLE_ADMINISTRATOR = 1;
    /** Role name */
    const NAME_ADMINISTRATOR = 'Administrator';

    /** Role id */
    const ROLE_MODERATOR = 2;
    /** Role name */
    const NAME_MODERATOR = 'Moderator';

    /** Role id */
    const ROLE_USER = 3;
    /** Role name */
    const NAME_USER = 'User';

    /**
     * @inheritdoc
     *
     * @throws DBALException
     * @throws Exception
     */
    public function run(): void
    {
        /** @var UuidFactoryInterface $uuid */
        $uuid = $this->getContainer()->get(UuidFactoryInterface::class);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID         => self::ROLE_ADMINISTRATOR,
            Model::FIELD_UUID       => $uuid->uuid4()->toString(),
            Model::FIELD_NAME       => self::NAME_ADMINISTRATOR,
            Model::FIELD_CREATED_AT => $this->now(),
        ]);
        $this->seedModelData(Model::class, [
            Model::FIELD_ID         => self::ROLE_MODERATOR,
            Model::FIELD_UUID       => $uuid->uuid4()->toString(),
            Model::FIELD_NAME       => self::NAME_MODERATOR,
            Model::FIELD_CREATED_AT => $this->now(),
        ]);
        $this->seedModelData(Model::class, [
            Model::FIELD_ID         => self::ROLE_USER,
            Model::FIELD_UUID       => $uuid->uuid4()->toString(),
            Model::FIELD_NAME       => self::NAME_USER,
            Model::FIELD_CREATED_AT => $this->now(),
        ]);
    }
}
