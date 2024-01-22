<?php

use App\Models\Role;
use App\Models\User;

test('user with role admin can access Role index', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $response = $this
        ->actingAs($user)
        ->getJson(route('admin.role.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data'
    ]);
});

// test('user with role admin can access Role store', function () {
//     $role = Role::factory()->create(['name' => 'admin']);

//     $user = User::factory()->create();
//     $user->roles()->sync([$role->id]);

//     $data = Role::factory()->make()->toArray();

//     $response = $this
//         ->actingAs($user)
//         ->postJson(route('admin.role.store'), $data);

//     $response->assertCreated();
//     $response->assertJsonStructure([
//         'message'
//     ]);

//     $this->assertDatabaseHas('roles', [
//         'name' => $data['name'],
//     ]);
// });

// // validation test
// test('user Role store validation', function () {
//     $role = Role::factory()->create(['name' => 'admin']);

//     $user = User::factory()->create();
//     $user->roles()->sync([$role->id]);

//     $data = Role::factory()->make()->toArray();

//     $response = $this
//         ->actingAs($user)
//         ->postJson(route('admin.role.store'));

//     $response->assertStatus(422);
//     $response->assertJsonStructure([
//         'message',
//         'errors' => []
//     ]);
// });

// test('user with role admin can access Role update', function () {
//     $role = Role::factory()->create(['name' => 'admin']);

//     $user = User::factory()->create();
//     $user->roles()->sync([$role->id]);

//     $Role = Role::factory()->create();
//     $data = Role::factory()->make()->toArray();

//     $response = $this
//         ->actingAs($user)
//         ->putJson(route('admin.role.update', $Role->id), $data);

//     $response->assertOk();
//     $response->assertJsonStructure([
//         'message'
//     ]);

//     $this->assertDatabaseHas('roles', [
//         'name' => $data['name'],
//     ]);
// });

// test('user Role update validation', function () {
//     $role = Role::factory()->create(['name' => 'admin']);

//     $user = User::factory()->create();
//     $user->roles()->sync([$role->id]);

//     $Role = Role::factory()->create();
//     $data = Role::factory()->make()->toArray();

//     $response = $this
//         ->actingAs($user)
//         ->putJson(route('admin.role.update', $Role->id), []);

//     $response->assertStatus(422);
//     $response->assertJsonStructure([
//         'message',
//         'errors' => []
//     ]);
// });

// test('user with role admin can access Role delete', function () {
//     $role = Role::factory()->create(['name' => 'admin']);

//     $user = User::factory()->create();
//     $user->roles()->sync([$role->id]);

//     $Role = Role::factory()->create();

//     $response = $this
//         ->actingAs($user)
//         ->deleteJson(route('admin.role.destroy', $Role->id));

//     $response->assertOk();
//     $response->assertJsonStructure([
//         'message'
//     ]);

//     $this->assertDatabaseMissing('roles', [
//         'id' => $Role->id,
//     ]);
// });
