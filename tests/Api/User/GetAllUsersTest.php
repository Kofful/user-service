<?php
declare(strict_types=1);

namespace Api\User;

use App\Repository\UserRepository;
use App\Tests\Api\ApiTestCase;

class GetAllUsersTest extends ApiTestCase
{
    public function testGetAllUsersUnauthorized(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->makeRequest();

        $this->assertResponseStatusCodeSame(401);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 401, 'message' => 'JWT Token not found'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testGetAllUsersAsAdmin(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest();

        // should see all logins
        $this->assertResponseStatusCodeSame(200);
        $response = json_decode($this->getClient()->getResponse()->getContent());
        $this->assertCount(4, $response);
    }

    public function testGetAllUsersAsUser(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsUser();
        $this->makeRequest();

        // should see only own logins
        $this->assertResponseStatusCodeSame(200);
        $response = json_decode($this->getClient()->getResponse()->getContent());
        $this->assertCount(2, $response);
    }

    public function testGetSpecificUserAsAdmin(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest('?login=test');

        // should see other user's logins
        $this->assertResponseStatusCodeSame(200);
        $response = json_decode($this->getClient()->getResponse()->getContent());
        $this->assertCount(2, $response);
    }

    public function testGetSpecificUserAsUser(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsUser();
        $this->makeRequest('?login=admin');

        // should not have access
        $this->assertResponseStatusCodeSame(403);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 403, 'message' => "You don't have permission to this login"];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    private function makeRequest(string $query = '')
    {
        return $this->getClient()->jsonRequest('GET', "/users$query");
    }
}
