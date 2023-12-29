<?php

namespace Tests\Feature;

use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use App\Models\Contact;
use Database\Seeders\SearchSeeder;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

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

    public function testGetContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'.$contact->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)->assertJson([
            'data' => [
                'first_name' => 'test',
                'last_name' => 'test',
                'email' => 'test@gmail.com',
                'phone' => '085641054319'
            ]
            ]);
    }

    public function testGetContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'. ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    "Not found."
                ]
            ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/'. ($contact->id + 1), [
            'Authorization' => 'test2'
        ])->assertStatus(404)->assertJson([
            'errors' => [
                'message' => [
                    "Not found."
                ]
            ]
            ]);
    }

    public function testUpdateContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/". $contact->id, 
        [
            "first_name" => "test2",
            "last_name" => "test2",
            "email" => "test2@gmail.com",
            "phone" => "085641054318"
        ],
        [ 
            "Authorization" => "test"
        ]
        )->assertStatus(200)->assertJson([
            "data" => [
                "first_name" => "test2",
                "last_name" => "test2",
                "email" => "test2@gmail.com",
                "phone" => "085641054318"
            ]
            ]);
    }

    public function testUpdateValidationFailure()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put("/api/contacts/". $contact->id, 
        [
            "first_name" => "",
            "last_name" => "test2",
            "email" => "test2@gmail.com",
            "phone" => "085641054318"
        ],
        [ 
            "Authorization" => "test"
        ]
        )->assertStatus(400)->assertJson([
            "errors" => [
                "first_name" => [
                    "The first name field is required."
                ]
            ]
            ]);
    }

    public function testDeleteContactSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete("/api/contacts/". $contact->id, [],
        [ 
            "Authorization" => "test"
        ]
        )->assertStatus(200)->assertJson([
            "data" => true
            ]);
    }

    public function testDeleteContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete("/api/contacts/". ($contact->id + 1), [],
        [ 
            "Authorization" => "test"
        ]
        )->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "Not found."
                ]
            ]
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=first',
        [ "Authorization" => "test" ]
        )->assertStatus(200)->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last',
        [ "Authorization" => "test" ]
        )->assertStatus(200)->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test',
        [ "Authorization" => "test" ]
        )->assertStatus(200)->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=0856',
        [ "Authorization" => "test" ]
        )->assertStatus(200)->json();

        Log::info(json_encode($response));
        self::assertEquals(10, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tidakada',
        [ "Authorization" => "test" ]
        )->assertStatus(200)->json();

        Log::info(json_encode($response));
        self::assertEquals(0, count($response['data']));
        self::assertEquals(0, $response['meta']['total']);
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2',
        [ "Authorization" => "test" ]
        )->assertStatus(200)->json();

        Log::info(json_encode($response));
        self::assertEquals(5, count($response['data']));
        self::assertEquals(20, $response['meta']['total']);
        self::assertEquals(2, $response['meta']['current_page']);
    }
}
