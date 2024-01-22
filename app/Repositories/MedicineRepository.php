<?php

namespace App\Repositories;

use App\Interfaces\Repositories\MedicineRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicineRepository extends BaseRepository implements MedicineRepositoryInterface
{
    /**
     * Instantiate a new MedicineRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name',
            'brand',
            'dosage_form',
            'strength',
            'indication',
            // 'description',
        ]);

        $this->setAllowableSort([
            'name',
            'brand',
            'dosage_form',
            'strength',
            'indication',
            // 'description',
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Medicine::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Medicine
    {
        $data =  Medicine::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Medicine
    {
        $model = Medicine::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Medicine::findOrFail($id);
        $data->delete();
    }

    /**
     * Get by ids repository
     */
    public function getByIds(array $ids): Collection
    {
        return Medicine::whereIn('id', $ids)->get();
    }
}
