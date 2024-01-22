<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Http\Requests\Artist\Artwork\StoreArtworkRequest;
use App\Http\Requests\Artist\Artwork\UpdateArtworkRequest;
use App\Interfaces\Services\ArtworkServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArtworkController extends Controller
{
    /**
     * Instantiate a new Controllers instance.
     */
    public function __construct()
    {
        // $this->policyModel = App\Models\Artwork::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ArtworkServiceInterface $artworkService)
    {
        return $this->responseJson($artworkService->datatableCurrentArtist(request()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArtworkRequest $request, ArtworkServiceInterface $artworkService)
    {
        // $this->authorize('create', App\Models\Artwork::class);

        DB::beginTransaction();

        $artworkService->create($request);
        try {
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'create', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Artwork $artwork)
    {
        // $this->authorize('view', $artwork);
        // service...

        return $this->responseJsonData($artwork);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtworkRequest $request, Artwork $artwork, ArtworkServiceInterface $artworkService)
    {
        // $this->authorize('update', $artwork);

        DB::beginTransaction();

        try {
            $artwork = $artworkService->update($request, $artwork);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'update', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Artwork $artwork, ArtworkServiceInterface $artworkService)
    {
        // $this->authorize('delete', $artwork);

        DB::beginTransaction();

        try {
            $artworkService->delete($artwork);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'delete', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'delete');
    }
}
