<?php


use App\Models\Artwork;
use App\Models\Role;
use App\Models\User;

test('user with role admin can access Artwork index', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $response = $this
        ->actingAs($user)
        ->getJson(route('admin.artwork.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data'
    ]);
});

test('user with role admin can access Artwork store', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->postJson(route('admin.artwork.store'), $data);

    $response->assertCreated();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseHas('artworks', [
        'name' => $data['name'],
    ]);
});

// validation test
test('user Artwork store validation', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->postJson(route('admin.artwork.store'));

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => []
    ]);
});

test('user with role admin can access Artwork update', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $Artwork = Artwork::factory()->create();
    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->putJson(route('admin.artwork.update', $Artwork->id), $data);

    $response->assertOk();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseHas('artworks', [
        'name' => $data['name'],
    ]);
});

test('user Artwork update validation', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $Artwork = Artwork::factory()->create();
    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->putJson(route('admin.artwork.update', $Artwork->id), []);

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => []
    ]);
});

test('user with role admin can access Artwork delete', function () {
    $role = Role::factory()->create(['name' => 'admin']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $Artwork = Artwork::factory()->create();

    $response = $this
        ->actingAs($user)
        ->deleteJson(route('admin.artwork.destroy', $Artwork->id));

    $response->assertOk();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseMissing('artworks', [
        'id' => $Artwork->id,
    ]);
});
