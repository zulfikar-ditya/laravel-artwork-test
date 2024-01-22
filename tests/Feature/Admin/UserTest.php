<?php


use App\Models\Role;
use App\Models\User;

test('user with role admin can access User index', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $response = $this
        ->actingAs($user)
        ->getJson(route('admin.user.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data'
    ]);
});

test('user with role admin can access User store', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $data = User::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->postJson(route('admin.user.store'), [
            ...$data,
            'role_id' => [$role->id],
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response->assertCreated();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseHas('users', [
        'name' => $data['name'],
    ]);
});

// validation test
test('user User store validation', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $data = User::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->postJson(route('admin.user.store'));

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => []
    ]);
});

test('user with role admin can access User update', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $User = User::factory()->create();
    $data = User::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->putJson(route('admin.user.update', $User->id), [
            ...$data,
            'role_id' => [$role->id]
        ]);

    $response->assertOk();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseHas('users', [
        'name' => $data['name'],
    ]);
});

test('user User update validation', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $User = User::factory()->create();
    $data = User::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->putJson(route('admin.user.update', $User->id), []);

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => []
    ]);
});

test('user with role admin can access User delete', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $User = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->deleteJson(route('admin.user.destroy', $User->id));

    $response->assertOk();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseMissing('users', [
        'id' => $User->id,
    ]);
});
