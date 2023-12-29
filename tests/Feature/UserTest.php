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

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::where("username", "test")->first();
        $this->patch("/api/users/current",
        ["password" => "new"],
        ["Authorization" => "test"]
        )->assertStatus(200)->assertJson([
            "data" => [
                "username" => "test",
                "name" => "test"
            ]
            ]);

        $newUser = User::where("username", "test")->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);

        $oldUser = User::where("username", "test")->first();
        $this->patch("/api/users/current",
        ["name" => "Misbah"],
        ["Authorization" => "test"]
        )->assertStatus(200)->assertJson([
            "data" => [
                "username" => "test",
                "name" => "Misbah"
            ]
            ]);

        $newUser = User::where("username", "test")->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailure()
    {
        $this->seed([UserSeeder::class]);

        $this->patch("/api/users/current",
        ["name" => "MisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestesMisbahtestestestestestestestestes"],
        ["Authorization" => "test"]
        )->assertStatus(400)->assertJson([
            "errors" => [
                "name" => [
                    "The name field must not be greater than 100 characters."
                ]
            ]
            ]);
    }
}
