<?php

namespace App\Repositories;

use App\Interfaces\Repositories\SpecialistRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SpecialistRepository extends BaseRepository implements SpecialistRepositoryInterface
{
    /**
     * Instantiate a new SpecialistRepository instance.
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
        return Specialist::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Specialist
    {
        $data =  Specialist::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Specialist
    {
        $model = Specialist::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Specialist::findOrFail($id);
        $data->delete();
    }
}
