<?php declare (strict_types=1);

namespace Tests\Api;

use App\Api\RolesApi as Api;
use App\Data\Seeds\RolesSeed as Seed;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleApiTest extends TestCase
{
    /**
     * Shows usage of low level API in tests.
     */
    public function testLowLevelApiRead(): void
    {
        $this->setPreventCommits();

        $this->setModerator();
        $api = $this->createApi(Api::class);

        $roleId = Seed::ROLE_USER;
        $this->assertNotNull($api->read((string)$roleId));
    }

    /**
     * Same test but with auth by a OAuth token.
     */
    public function testLowLevelApiReadWithAuthByToken(): void
    {
        $this->setPreventCommits();

        $oauthToken  = $this->getModeratorOAuthToken();
        $accessToken = $oauthToken->access_token;
        $this->setUserByToken($accessToken);

        $api = $this->createApi(Api::class);

        $roleId = Seed::ROLE_USER;
        $this->assertNotNull($api->read((string)$roleId));
    }
}
