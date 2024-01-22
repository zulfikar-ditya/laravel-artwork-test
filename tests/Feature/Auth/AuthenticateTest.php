<?php

test('user guest can login', function () {

    $user = \App\Models\User::factory()->create();

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        "message",
        "data" => [
            "access_token",
            "token_type",
            "user_information",
        ],
    ]);
});

test("user cannot login with invalid email", function () {

    $user = \App\Models\User::factory()->create();

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->post('/api/login', [
            'email' => 'foo@bar.com',
            'password' => 'password',
        ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            "message",
            "errors" => [
                "email"
            ],
        ]);
});


test("user cannot login with invalid password", function () {
    $user = \App\Models\User::factory()->create();

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->post('/api/login', [
            'email' => $user->email,
            'password' => '<PASSWORD>',
        ]);

    $response->assertStatus(422)
        ->assertJsonStructure([
            "message",
            "errors",
        ]);
});


test("user can login with valid credentials", function () {
    $user = \App\Models\User::factory()->create();

    $response = $this
        ->withHeaders([
            'Accept' => 'application/json',
        ])
        ->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        "message",
        "data" => [
            "access_token",
            "token_type",
            "user_information",
        ],
    ]);
});


// test("user can logout", function () {
//     $user = \App\Models\User::factory()->create();

//     $response = $this
//         ->withHeaders([
//             'Accept' => 'application/json',
//         ])
//         ->actingAs($user)
//         ->post('/api/logout');

//     $response->assertStatus(401);
//     $response->assertJsonStructure([
//         "message",
//     ]);
// });
