<?php
declare(strict_types=1);

namespace Api\User;

use App\Dto\User\CreateUserDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Api\ApiTestCase;

class CreateUserTest extends ApiTestCase
{
    public function testCreateUserUnauthorized(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->makeRequest();

        $this->assertResponseStatusCodeSame(401);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 401, 'message' => 'JWT Token not found'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testEmptyRequest(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest();

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => "The login field is required.\nThe pass field is required.\nThe phone field is required."];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testLoginIsTooLong(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest([
            'login' => 'aaaaaaaaaaaaaaaaa',
            'pass' => 'pass',
            'phone' => '11111',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The login field must be shorter than 8 characters.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testPassIsTooLong(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest([
            'login' => 'new',
            'pass' => 'aaaaaaaaaaaaaaaaa',
            'phone' => '11111',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The pass field must be shorter than 8 characters.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testPhoneIsTooLong(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest([
            'login' => 'new',
            'pass' => 'pass',
            'phone' => '11111111111111',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The phone field must be shorter than 8 characters.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testPhoneIsNotNumeric(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest([
            'login' => 'new',
            'pass' => 'pass',
            'phone' => 'aaaaaaa',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The phone field must contain only numbers.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testLoginAndPasswordAlreadyExist(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest([
            'login' => 'admin',
            'pass' => 'pass',
            'phone' => '11111',
        ]);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The login and password are already in use.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testCreateUserAsAdmin(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsAdmin();
        $this->makeRequest([
            'login' => 'new',
            'pass' => 'pass',
            'phone' => '11111',
        ]);

        $user = $this->userRepository->findOneBy(['login' => 'new']);

        $this->assertResponseStatusCodeSame(201);
        $response = json_decode($this->getClient()->getResponse()->getContent(), true);
        $this->assertEquals($this->getExpectedResponse($user), $response);
    }

    public function testCreateAnotherUserAsUser(): void
    {
        self::ensureKernelShutdown();
        self::createClient();

        $this->loginAsUser();
        $this->makeRequest([
            'login' => 'admin',
            'pass' => 'pass12',
            'phone' => '11111',
        ]);

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

    private function makeRequest(array $data = [])
    {
        return $this->getClient()->jsonRequest('POST', "/users", $data);
    }
}
