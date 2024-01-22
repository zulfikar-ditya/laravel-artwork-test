<?php

use App\Models\Artwork;
use App\Repositories\ArtworkRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

it('can get datatable', function () {
    $repository = new ArtworkRepository();
    $request = new Request([
        'search' => 'search term',
        'order' => '-name',
        'per_page' => 10,
    ]);

    $result = $repository->datatable($request);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('can create a Artwork', function () {
    $repository = new ArtworkRepository();
    $data = Artwork::factory()->make()->toArray();

    $result = $repository->create($data);

    expect($result)->toBeInstanceOf(Artwork::class);
    expect($result->name)->toEqual($data['name']);

    $this->assertDatabaseHas('artworks', [
        'name' => $data['name'],
    ]);
});

it('can update a Artwork', function () {
    $repository = new ArtworkRepository();
    $Artwork = Artwork::factory()->create();

    $data = [
        'name' => 'Jane Smith',
    ];

    $result = $repository->update($data, $Artwork->id);

    expect($result)->toBeInstanceOf(Artwork::class);
    expect($result->name)->toEqual('Jane Smith');

    $this->assertDatabaseHas('artworks', [
        'name' => 'Jane Smith',
    ]);
});

it('can delete a Artwork', function () {
    $repository = new ArtworkRepository();
    $Artwork = Artwork::factory()->create();

    $repository->delete($Artwork->id);

    expect(Artwork::find($Artwork->id))->toBeNull();

    $this->assertDatabaseMissing('artworks', [
        'name' => $Artwork->name,
    ]);
});
