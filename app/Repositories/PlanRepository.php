<?php

namespace App\Repositories;

use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanRepository extends BaseRepository implements PlanRepositoryInterface
{
    /**
     * Instantiate a new PlanRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name',
            'price',
            'billing_cycle',
            'description',
        ]);

        $this->setAllowableSort([
            'name',
            'price',
            'billing_cycle',
            'description',
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Plan::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Plan
    {
        $data =  Plan::create($data);

        return $data;
    }

    /**
     * show repository
     */
    public function show(string $id): Plan
    {
        return Plan::findOrFail($id);
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Plan
    {
        $model = Plan::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Plan::findOrFail($id);
        $data->delete();
    }
}
