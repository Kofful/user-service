<?php

namespace App\Tests\Api\Auth;

use App\Tests\Api\ApiTestCase;

class LoginTest extends ApiTestCase
{
    public function testEmptyRequest(): void
    {
        $this->makeRequest();

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => "The login field is required.\nThe pass field is required."];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testLoginIsTooLong(): void
    {
        $this->makeRequest(['login' => 'aaaaaaaaaaaaaa', 'pass' => 'pass']);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The login field must be shorter than 8 characters.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testPassIsTooLong(): void
    {
        $this->makeRequest(['login' => 'test', 'pass' => 'aaaaaaaaaaaaaa']);

        $this->assertResponseStatusCodeSame(422);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 422, 'message' => 'The pass field must be shorter than 8 characters.'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testWrongCredentials(): void
    {
        $this->makeRequest(['login' => 'wrong', 'pass' => 'wrong']);

        $this->assertResponseStatusCodeSame(401);
        $response = $this->getClient()->getResponse();
        $expectedResponse = ['code' => 401, 'message' => 'Invalid credentials'];
        $this->assertEquals($expectedResponse, json_decode($response->getContent(), true));
    }

    public function testSuccessfulLogin(): void
    {
        $this->makeRequest(['login' => 'test', 'pass' => 'pass']);

        $this->assertResponseStatusCodeSame(200);
        $response = json_decode($this->getClient()->getResponse()->getContent());
        $this->assertObjectHasProperty('token', $response);
    }

    private function makeRequest(array $data = [])
    {
        self::ensureKernelShutdown();
        return static::createClient()->jsonRequest('POST', '/login', $data);
    }
}
