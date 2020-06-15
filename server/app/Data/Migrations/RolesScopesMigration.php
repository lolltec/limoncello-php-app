<?php declare (strict_types=1);

namespace App\Data\Migrations;

use App\Data\Models\Role;
use App\Data\Models\RoleScope as Model;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Type;
use Limoncello\Contracts\Data\MigrationInterface;
use Limoncello\Data\Migrations\RelationshipRestrictions;
use Limoncello\Passport\Entities\DatabaseSchema;
use Limoncello\Passport\Entities\Scope;
use Lolltec\Limoncello\Data\Migrations\MigrationTrait;

/**
 * @package App
 */
class RolesScopesMigration implements MigrationInterface
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
            $this->foreignRelationship(Model::FIELD_ID_ROLE, Role::class, RelationshipRestrictions::CASCADE),
            $this->foreignColumn(
                Model::FIELD_ID_SCOPE,
                DatabaseSchema::TABLE_SCOPES,
                Scope::FIELD_ID,
                Type::STRING,
                255
            ),
            $this->timestamps(),
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
