<?php

namespace Tests\Feature;

use Tests\TestCase;

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
}
