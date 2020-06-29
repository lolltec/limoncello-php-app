<?php declare (strict_types=1);

namespace Tests\Json;

use App\Data\Models\User as Model;
use App\Data\Seeds\RolesSeed;
use App\Data\Seeds\UsersSeed as Seed;
use App\Json\Schemas\UserSchema as Schema;
use Limoncello\Contracts\Http\ThrowableResponseInterface;
use Limoncello\Testing\JsonApiCallsTrait;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Tests\TestCase;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    use JsonApiCallsTrait;

    const API_URI = '/api/v1/' . Schema::TYPE;

    /**
     * Test User's API.
     */
    public function testIndex()
    {
        $this->setPreventCommits();

        $response = $this->get(self::API_URI, [], $this->getAdminOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(3, $json->data);
    }

    /**
     * Test index with parameters.
     */
    public function testIndexWithInclude()
    {
        $this->setPreventCommits();

        $queryParams = [
            'filter'  => [
                'id'        => [
                    'gte' => '1',  // 'long' form for condition operations
                    'lte' => '5',  // 'short' form supported as well
                ],
                'role.name' => [
                    'like' => '%%',          // example how conditions could be applied to relationships' attributes
                ],
            ],
            'sort'    => '+id,-first-name', // example of how multiple sorting conditions could be applied
            'include' => 'role',
            'fields'  => [
                'users' => 'id,first-name,role',
            ],
        ];

        $headers  = $this->getAdminOAuthHeader();
        $response = $this->get(self::API_URI, $queryParams, $headers);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(3, $resources->data);

        $resource = $resources->data[0];
        $this->assertEquals(1, $resource->id);
        $this->assertEquals(RolesSeed::ROLE_ADMINISTRATOR, $resource->relationships->role->data->id);

        $resource = $resources->data[2];
        $this->assertEquals(3, $resource->id);
        $this->assertEquals(RolesSeed::ROLE_USER, $resource->relationships->role->data->id);

        // check response has included posts as well
        $this->assertCount(3, $resources->included);
    }

    /**
     * Test User's API.
     */
    public function testIndexInvalidToken()
    {
        $response = $this->get(self::API_URI, [], $this->getOAuthHeader('XXX'));
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test User's API.
     */
    public function testRead()
    {
        $this->setPreventCommits();

        $userId   = '1';
        $response = $this->get(self::API_URI . "/$userId", [], $this->getAdminOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($userId, $json->data->id);
        $this->assertEquals(Schema::TYPE, $json->data->type);
    }

    /**
     * Test User's API.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $userId  = '2';
        $headers = $this->getAdminOAuthHeader();

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // check user do not exist
        $this->assertEquals(404, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());
    }

    /**
     * Test User's API.
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $role      = RolesSeed::ROLE_USER;
        $password  = Seed::DEFAULT_PASSWORD;
        $email     = "john@dow.foo";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "email"      : "$email",
                    "password"   : "$password"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "$role" }
                    }
                }
            }
        }
EOT;
        $headers   = $this->getAdminOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $userId = $json->data->id;

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($userId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test User's API.
     */
    public function testCreateInvalidData()
    {
        $this->setPreventCommits();

        $password  = 'secret';
        $email     = "it_does_not_look_like_an_email";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "email"      : "$email",
                    "password"   : "$password"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "user" }
                    }
                }
            }
        }
EOT;
        /** @var ThrowableResponseInterface $response */
        $this->assertInstanceOf(
            ThrowableResponseInterface::class,
            $response = $this->postJsonApi(self::API_URI, $jsonInput, $this->getAdminOAuthHeader())
        );
        /** @var JsonApiException $exception */
        $this->assertInstanceOf(JsonApiException::class, $exception = $response->getThrowable());

        $this->assertCount(3, $exception->getErrors());
        $error = $exception->getErrors()->getArrayCopy()[0];
        $this->assertEquals('The value should be a valid email address.', $error->getDetail());
    }

    /**
     * Test User's API.
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $role      = RolesSeed::ROLE_ADMINISTRATOR;
        $userId    = 2;
        $role      = RolesSeed::ROLE_ADMINISTRATOR;
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "id"   : "$userId",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "password"   : "new-secret"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "$role" }
                    }
                }
            }
        }
EOT;
        $headers   = $this->getAdminOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$userId", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($userId, $json->data->id);

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query     = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($userId))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals('John', $values[Model::FIELD_FIRST_NAME]);
        $this->assertEquals('Dow', $values[Model::FIELD_LAST_NAME]);
        $this->assertNotEmpty($values[Model::FIELD_UPDATED_AT]);
        $this->assertEquals(RolesSeed::ROLE_ADMINISTRATOR, $values[Model::FIELD_ID_ROLE]);
    }

    /**
     * Test User's API.
     */
    public function testUnauthorizedDenied()
    {
        // no token header

        $response = $this->get(self::API_URI);
        $this->assertEquals(403, $response->getStatusCode());

        /** @var ThrowableResponseInterface $response */
        $this->assertInstanceOf(
            ThrowableResponseInterface::class,
            $response = $this->get(self::API_URI)
        );
        /** @var JsonApiException $exception */
        $this->assertInstanceOf(JsonApiException::class, $exception = $response->getThrowable());

        $this->assertCount(1, $exception->getErrors());
        $error = $exception->getErrors()->getArrayCopy()[0];
        $this->assertEquals('You are not unauthorized for action `canViewUsers`.', $error->getDetail());
    }
}
