<?php

// https://api.kaktoos.example/api/login

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_api()
    {
        // $request = $this->get('/sanctum/csrf-cookie');
        // $response = $this
        //     // ->withHeader('X-CSRF-TOKEN', $request->getCookie('XSRF-TOKEN')->getValue())
        //     // ->withHeader('Referer', 'kaktoos.example')
        //     ->postJson('/api/login', [
        //         "phone_number" =>  "09150013422",
        //         "code" =>  "91631"
        //     ]);

        //     $response->dd();
        // return;
        // // $response->assertStatus(200);

        // $response = $this
        //     ->withHeader('Accept', 'application/json')
        //     ->withHeader('Referer', 'kaktoos.example')
        //     ->postJson('/api/login', [
        //         "phone_number" =>  "09150013422",
        //         "code" =>  "91631"
        //     ]);
        // $response->assertStatus(403);
        // $this->assertTrue(
        //     str_contains($response->json('message'), 'logged in')
        // );

        // $response = $this->getJson('/api/user');
        // $this->assertArrayHasKey('id', $response->json());
    }
}