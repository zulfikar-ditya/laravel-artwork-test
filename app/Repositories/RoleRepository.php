<?php

namespace App\Repositories;

use App\Interfaces\Repositories\RoleRepositoryInterface;
use App\Models\Role;
use App\Repositories\Base\BaseRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleRepository extends BaseRepository implements RoleRepositoryInterface
{
    /**
     * Instantiate a new RoleRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name'
        ]);

        $this->setAllowableSort([
            'name'
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Role::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Role
    {
        $data =  Role::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Role
    {
        $model = Role::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Role::findOrFail($id);
        $data->delete();
    }
}
