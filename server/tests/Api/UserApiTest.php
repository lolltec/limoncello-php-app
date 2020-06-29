<?php declare (strict_types=1);

namespace Tests\Api;

use App\Api\UsersApi as Api;
use App\Data\Models\User as Model;
use App\Data\Seeds\UsersSeed as Seed;
use Doctrine\DBAL\DBALException;
use Limoncello\Contracts\Exceptions\AuthorizationExceptionInterface;
use Tests\TestCase;
use function assert;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    /**
     * Sample how to test low level API.
     */
    public function testLowLevelApi()
    {
        $this->setPreventCommits();

        // create API

        /** @var Api $api */
        $api = $this->createUsersApi();

        // Call and check any method from low level API.

        /** Default seed data. Manually checked. */
        $this->assertEquals(1, $api->noAuthReadUserIdByEmail(Seed::USER_ADMINISTRATOR));
    }

    /**
     * Test for password reset.
     *
     * @throws DBALException
     * @throws AuthorizationExceptionInterface
     */
    public function testResetPassword()
    {
        $this->setPreventCommits();

        // create APIs

        $noAuthApi = $this->createUsersApi();

        $this->setAdmin();
        $api = $this->createUsersApi();

        // Call reset method.
        $userId = 1;
        $before = $api->read((string)$userId);
        $this->assertTrue($noAuthApi->noAuthResetPassword($userId, 'new password'));
        $after = $api->read((string)$userId);
        $this->assertNotEquals($before->{Model::FIELD_PASSWORD_HASH}, $after->{Model::FIELD_PASSWORD_HASH});
    }

    /**
     * @return Api
     */
    private function createUsersApi(): Api
    {
        $api = $this->createApi(Api::class);
        assert($api instanceof Api);

        return $api;
    }

}
