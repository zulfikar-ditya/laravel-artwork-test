<?php


use App\Models\Artwork;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;

test('user with role artist can access Artwork index', function () {
    $role = Role::factory()->create(['name' => 'artist']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $response = $this
        ->actingAs($user)
        ->getJson(route('artist.artwork.index'));

    $response->assertOk();
    $response->assertJsonStructure([
        'data'
    ]);
});

test('user with role artist can access Artwork store', function () {
    $role = Role::factory()->create(['name' => 'artist']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->postJson(route('artist.artwork.store'), $data);

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
    $role = Role::factory()->create(['name' => 'artist']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->postJson(route('artist.artwork.store'));

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => []
    ]);
});

test('user with role artist can access Artwork update', function () {
    $role = Role::factory()->create(['name' => 'artist']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $Artwork = Artwork::factory()->create();
    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->putJson(route('artist.artwork.update', $Artwork->id), $data);

    $response->assertOk();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseHas('artworks', [
        'name' => $data['name'],
    ]);
});

test('user Artwork update validation', function () {
    $role = Role::factory()->create(['name' => 'artist']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $Artwork = Artwork::factory()->create();
    $data = Artwork::factory()->make()->toArray();

    $response = $this
        ->actingAs($user)
        ->putJson(route('artist.artwork.update', $Artwork->id), []);

    $response->assertStatus(422);
    $response->assertJsonStructure([
        'message',
        'errors' => []
    ]);
});

test('user with role artist can access Artwork delete', function () {
    $role = Role::factory()->create(['name' => 'artist']);

    $user = User::factory()->create();
    $user->roles()->sync([$role->id]);

    $Artwork = Artwork::factory()->create();

    $response = $this
        ->actingAs($user)
        ->deleteJson(route('artist.artwork.destroy', $Artwork->id));

    $response->assertOk();
    $response->assertJsonStructure([
        'message'
    ]);

    $this->assertDatabaseMissing('artworks', [
        'id' => $Artwork->id,
    ]);
});
