<?php
declare(strict_types=1);

namespace Api\User;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Api\ApiTestCase;

class GetUserTest extends ApiTestCase
{
    public function testGetUserUnauthorized(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->makeRequest(1);

        $this->assertResponseStatusCodeSame(401);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 401, 'message' => 'JWT Token not found'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testGetUserAsAdmin(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $user = $this->userRepository->find(ApiTestCase::USER_ID);

        $this->loginAsAdmin();
        $this->makeRequest(ApiTestCase::USER_ID);

        // should see the login
        $this->assertResponseStatusCodeSame(200);
        $response = json_decode($this->getClient()->getResponse()->getContent(), true);
        $this->assertEquals($this->getExpectedResponse($user), $response);
    }

    public function testGetSelfAsUser(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $user = $this->userRepository->find(ApiTestCase::USER_ID);

        $this->loginAsUser();
        $this->makeRequest(ApiTestCase::USER_ID);

        // should see only own logins
        $this->assertResponseStatusCodeSame(200);
        $response = json_decode($this->getClient()->getResponse()->getContent(), true);
        $this->assertEquals($this->getExpectedResponse($user), $response);
    }

    public function testGetSpecificUserAsUser(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsUser();
        $this->makeRequest(ApiTestCase::ADMIN_ID);

        // should not have access
        $this->assertResponseStatusCodeSame(403);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 403, 'message' => "You don't have permission to this login"];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    private function getExpectedResponse(User $user)
    {
        return [
            'id' => $user->getId(),
            'login' => $user->getLogin(),
            'pass' => $user->getPassword(),
            'phone' => $user->getPhone(),
        ];
    }

    private function makeRequest(int $id)
    {
        return $this->getClient()->jsonRequest('GET', "/users/$id");
    }
}
