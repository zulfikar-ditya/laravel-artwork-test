<?php

namespace App\Repositories;

use App\Interfaces\Repositories\PolyRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Poly;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PolyRepository extends BaseRepository implements PolyRepositoryInterface
{
    /**
     * Instantiate a new PolyRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name',
        ]);

        $this->setAllowableSort([
            'name',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Poly::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Poly
    {
        $data =  Poly::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Poly
    {
        $model = Poly::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Poly::findOrFail($id);
        $data->delete();
    }
}
