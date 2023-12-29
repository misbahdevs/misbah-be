<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateContactSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', 
        [
            "first_name" => "misbah",
            "last_name" => "hulmunir",
            "email" => "misbahx.id@gmail.com",
            "phone" => "085641054319"
        ],
        [
            "Authorization" => "test"
        ])->assertStatus(201)->assertJson([
            "data" => [
                "first_name" => "misbah",
                "last_name" => "hulmunir",
                "email" => "misbahx.id@gmail.com",
                "phone" => "085641054319"
            ]
            ]);
    }

    public function testCreateContactFailure()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', 
        [
            "first_name" => "",
            "last_name" => "hulmunir",
            "email" => "misbahx",
            "phone" => "085641054319"
        ],
        [
            "Authorization" => "test"
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "first_name" => [
                    "The first name field is required."
                ],
                "email" => [
                    "The email field must be a valid email address."
                ]
            ]
            ]);
    }
}
