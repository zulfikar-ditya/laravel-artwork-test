<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ArtworkRepositoryInterface;
use App\Models\Artwork;
use App\Repositories\Base\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArtworkRepository extends BaseRepository implements ArtworkRepositoryInterface
{
    /**
     * Instantiate a new ArtworkRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name',
            'user.name',
        ]);

        $this->setAllowableSort([
            'name'
        ]);

        $this->setAllowableFilter([
            'user_id'
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Artwork::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->filter and is_array($request->filder), function ($query) use ($request) {
                $this->getAllowableFilterQuery($query, $request->filter);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository
     */
    public function datatableCurrentArtist(Request $request): LengthAwarePaginator
    {
        return Artwork::query()
            ->where('user_id', auth()->id())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Artwork
    {
        $data =  Artwork::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Artwork
    {
        $model = Artwork::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Artwork::findOrFail($id);
        $data->delete();
    }
}
