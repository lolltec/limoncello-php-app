<?php declare (strict_types=1);

namespace App\Data\Migrations;

use App\Data\Models\Role as Model;
use Doctrine\DBAL\DBALException;
use Limoncello\Contracts\Data\MigrationInterface;
use Lolltec\Limoncello\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class RolesMigration implements MigrationInterface
{
    use MigrationTrait;

    /**
     * @inheritdoc
     *
     * @throws DBALException
     */
    public function migrate(): void
    {
        $this->createTable(Model::class, [
            $this->primaryInt(Model::FIELD_ID),
            $this->defaultUuid(),
            $this->string(Model::FIELD_NAME),
            $this->nullableText(Model::FIELD_DESCRIPTION),
            $this->timestamps(),

            $this->unique([Model::FIELD_UUID]),
            $this->unique([Model::FIELD_NAME]),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rollback(): void
    {
        $this->dropTableIfExists(Model::class);
    }
}
