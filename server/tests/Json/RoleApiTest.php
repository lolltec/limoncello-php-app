<?php declare (strict_types=1);

namespace Tests\Json;

use App\Data\Models\Role as Model;
use App\Data\Seeds\RolesSeed as Seed;
use App\Data\Seeds\UsersSeed;
use App\Json\Schemas\RoleSchema as Schema;
use Limoncello\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . Schema::TYPE;

    /**
     * Test Role's API.
     */
    public function testIndex()
    {
        $this->setPreventCommits();

        $response = $this->get(self::API_URI, [], $this->getModeratorOAuthHeader());
        $token    = $this->getOAuthToken(UsersSeed::USER_MODERATOR, UsersSeed::DEFAULT_PASSWORD);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(3, $json->data);
    }

    /**
     * Test Role's API.
     */
    public function testRead()
    {
        $this->setPreventCommits();

        $roleId   = Seed::ROLE_USER;
        $response = $this->get(self::API_URI . "/$roleId", [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($roleId, $json->data->id);
        $this->assertEquals(Schema::TYPE, $json->data->type);
    }

    /**
     * Test Role's API.
     */
    public function testReadRelationships()
    {
        $this->setPreventCommits();

        $roleId   = Seed::ROLE_ADMINISTRATOR;
        $response = $this->get(self::API_URI . "/$roleId/users", [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(1, $json->data);

        $relationship = $json->data[0];
        $this->assertEquals(1, $relationship->id);
    }

    /**
     * Test Role's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $name      = "New role";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "attributes" : {
                    "name"  : "$name"
                }
            }
        }
EOT;
        $headers   = $this->getAdminOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $roleId = $json->data->id;

        // check role exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$roleId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($roleId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test Role's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index       = Seed::ROLE_USER;
        $description = "New description";
        $jsonInput   = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "id"    : "$index",
                "attributes" : {
                    "description" : "$description"
                }
            }
        }
EOT;
        $headers     = $this->getAdminOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$index", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($index, $json->data->id);

        // check role exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$index", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($index))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals($description, $values[Model::FIELD_DESCRIPTION]);
        $this->assertNotEmpty($values[Model::FIELD_UPDATED_AT]);
    }
}
