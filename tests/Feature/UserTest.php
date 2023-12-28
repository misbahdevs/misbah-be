<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotNull;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post("/api/users", [
            "username" => "misbahdev",
            "password" => "rahasia",
            "name" => "Misbah"
        ])->assertStatus(201)->assertJson([
            "data" => [
                "username" => "misbahdev",
                "name" => "Misbah"
            ]
            ]);
    }

    public function testRegisterFailure()
    {
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => ""
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "The username field is required."
                ],
                "password" => [
                    "The password field is required."
                ],
                "name" => [
                    "The name field is required."
                ]
            ]
                ]);
    }

    public function testRegisterUsernameAlreadyExist()
    {
        $this->testRegisterSuccess();
        $this->post("/api/users", [
            "username" => "misbahdev",
            "password" => "rahasia",
            "name" => "Misbah"
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "username already registered."
                ]
            ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test",
        ])->assertStatus(200)->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test"
            ]
            ]);

        $user = User::where('username', 'test')->first();
        self::assertNotNull($user->token);
    }

    public function testLoginFailureUserNotFound()
    {
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test",
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "username or password wrong."
                ]
            ]
            ]);
    }

    public function testLoginWrongPassword()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "salah",
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "username or password wrong."
                ]
            ]
            ]);
    }
}
