<?php

namespace App\Repositories;

use App\Interfaces\Repositories\MedicineBatchRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\MedicineBatch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicineBatchRepository extends BaseRepository implements MedicineBatchRepositoryInterface
{
    /**
     * Instantiate a new MedicineBatchRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'clinic.name',
            'createdId.name',
            'createdId.email',
            'code',
            'date',
            // 'type',
        ]);

        $this->setAllowableSort([
            'clinic_id',
            'created_id',
            'code',
            'date',
            // 'type',
        ]);

        $this->setAllowableInclude([
            'clinic' => function ($q) {
                $q->select([
                    'id',
                    'name',
                    'sort',
                ]);
            },
            'createdId' => function ($q) {
                $q->select([
                    'id',
                    'name',
                    'email',
                ]);
            },
        ]);

        $this->setAllowableFilter([
            'clinic_id',
            'created_id',
            'date',
            'type',
        ]);
    }

    /**
     * datatable repository query
     */
    private function datatableQuery(Request $request)
    {
        return MedicineBatch::query()
            ->with($this->getAllowableInclude())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->filter && is_array($request->filter), function ($query) use ($request) {
                $this->getAllowableFilterQuery($query, $request->filter);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection());
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this->datatableQuery($request)
            ->where('is_merge', false)
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository is_merge
     */
    public function datatableIsMerge(Request $request): LengthAwarePaginator
    {
        return $this->datatableQuery($request)
            ->where('is_merge', true)
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): MedicineBatch
    {
        $data =  MedicineBatch::create($data);

        return $data;
    }

    /**
     * Show repository
     */
    public function show(string $id): MedicineBatch
    {
        $with = [
            ...$this->getAllowableInclude(),
            'medicineBatchDetails.medicine',
        ];

        return MedicineBatch::with($with)->findOrFail($id);
    }

    /**
     * Show repository merged
     */
    public function showMerged(string $id): MedicineBatch
    {
        $with = [
            ...$this->getAllowableInclude(),
            'medicineBatchDetails' => function ($q) {
                $q->with([
                    'medicine', 'mergeMedicine' => function ($q) {
                        return $q->with([
                            'medicineBatch.medicineBatchDetails', 'medicineForm',
                        ]);
                    }
                ]);
            },
        ];

        return MedicineBatch::with($with)
            ->withoutGlobalScope('dataMerge')
            ->findOrFail($id);
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): MedicineBatch
    {
        $model = MedicineBatch::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = MedicineBatch::findOrFail($id);
        $data->delete();
    }
}
