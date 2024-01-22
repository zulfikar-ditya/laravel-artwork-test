<?php

namespace App\Services;

use App\Interfaces\Repositories\ArtworkRepositoryInterface;
use App\Interfaces\Services\ArtworkServiceInterface;
use App\Models\Artwork;
use App\Services\Base\BaseService;
use App\Traits\UploadFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArtworkService extends BaseService implements ArtworkServiceInterface
{
    use UploadFile;

    /**
     * The constructor service
     */
    public function __construct(private ArtworkRepositoryInterface $repository)
    {
        //
    }

    /**
     * Datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this->repository->datatable($request);
    }

    /**
     * Datatable service for current artist
     */
    public function datatableCurrentArtist(Request $request): LengthAwarePaginator
    {
        return $this->repository->datatableCurrentArtist($request);
    }

    /**
     * Create service
     */
    public function create(FormRequest $request): void
    {
        $this->repository->create([
            ...$request->safe()->except('path'),
            'path' => $request->hasFile('path') ? $this->uploadFile($request->file('path'), 'artworks') : '',
        ]);
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, Artwork $Artwork): void
    {
        $oldFIle = $Artwork->path;

        $this->repository->update([
            ...$request->safe()->except('path'),
            'path' => $request->hasFile("path") ? $this->uploadFile($request->file('path'), 'artworks', $oldFIle) : $oldFIle,
        ], $Artwork->id);

        if ($request->hasFile("path")) {
            $this->deleteFile($oldFIle);
        }
    }

    /**
     * Delete service
     */
    public function delete(Artwork $Artwork): void
    {
        $this->repository->delete($Artwork->id);
    }
}
