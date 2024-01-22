<?php

use App\Http\Requests\Admin\Artwork\StoreArtworkRequest;
use App\Interfaces\Services\ArtworkServiceInterface;
use App\Models\Artwork;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;

it('can get datatable', function () {
    $request = new Request([
        'search' => 'search term',
        'order' => '-name',
        'per_page' => 10,
    ]);

    $service = app(ArtworkServiceInterface::class);

    $result = $service->datatable($request);

    expect($result)->toBeInstanceOf(LengthAwarePaginator::class);
});

it('can create a Artwork', function () {

    $fake = Artwork::factory()->make()->toArray();
    $fake['path'] = UploadedFile::fake()->image('path.jpg');

    $request = new FormRequest();
    $request = $request->setValidator(Validator::make(
        $fake,
        (new StoreArtworkRequest())->rules()
    ));

    $service = app(ArtworkServiceInterface::class);

    $result = $service->create($request);

    $this->assertDatabaseHas('artworks', [
        'name' => $fake['name'],
    ]);
});

it('can update a Artwork', function () {
    $Artwork = Artwork::factory()->create();

    $fake = Artwork::factory()->make()->toArray();
    $fake['path'] = UploadedFile::fake()->image('path.jpg');

    $request = new FormRequest();
    $request = $request->setValidator(Validator::make(
        $fake,
        (new StoreArtworkRequest())->rules()
    ));

    $service = app(ArtworkServiceInterface::class);

    $result = $service->update($request, $Artwork);

    $this->assertDatabaseHas('artworks', [
        'name' => $fake['name'],
    ]);
});

it('can delete a Artwork', function () {
    $Artwork = Artwork::factory()->create();

    $service = app(ArtworkServiceInterface::class);

    $result = $service->delete($Artwork);

    expect(Artwork::find($Artwork->id))->toBeNull();

    $this->assertDatabaseMissing('artworks', [
        'name' => $Artwork->name,
    ]);
});
