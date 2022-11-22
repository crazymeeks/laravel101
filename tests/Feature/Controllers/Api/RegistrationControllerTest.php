<?php

namespace Tests\Feature\Controllers\Api;

use Tests\TestCase;

class RegistrationControllerTest extends TestCase
{


    public function testShouldRegisterUser()
    {

        $request = [
            'username' => 'jdoe',
            'email' => 'john.doe@example.com',
            'password' => 'P@ssword12345',
            'password_confirmation' => 'P@ssword12345',
        ];
        $response = $this->json('POST', route('api.v1.post.signup'), $request);

        $this->assertDatabaseHas('users', [
            'username' => 'jdoe',
            'email' => 'john.doe@example.com'
        ]);

        $response->assertStatus(200);
    }
}