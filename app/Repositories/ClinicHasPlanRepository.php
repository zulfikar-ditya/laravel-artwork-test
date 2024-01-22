<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ClinicHasPlanRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\ClinicHasPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClinicHasPlanRepository extends BaseRepository implements ClinicHasPlanRepositoryInterface
{
    /**
     * Instantiate a new ClinicHasPlanRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'clinic.name',
            'clinic.sort',
            'plan.name',
            'billing_cycle',
            'from',
            'to',
            'active',
        ]);

        $this->setAllowableSort([
            'clinic_id',
            'plan_id',
            'billing_cycle',
            'from',
            'to',
            'active',
        ]);

        $this->setAllowableInclude([
            'clinic' => function ($q) {
                $q->select('id', 'name', 'sort');
            },
            'plan' => function ($q) {
                $q->select('id', 'name');
            },
        ]);

        $this->setAllowableFilter([
            'clinic_id',
            'plan_id',
            'billing_cycle',
            'from',
            'to',
            'active',
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return ClinicHasPlan::query()
            ->with($this->getAllowableInclude())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->filter && is_array($request->filter), function ($query, $filter) {
                $this->getAllowableFilterQuery($query, $filter);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): ClinicHasPlan
    {
        $data =  ClinicHasPlan::create($data);

        return $data;
    }

    /**
     * Show repository
     */
    public function show(string $id): ClinicHasPlan
    {
        return ClinicHasPlan::with($this->getAllowableInclude())->findOrFail($id);
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): ClinicHasPlan
    {
        $model = ClinicHasPlan::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = ClinicHasPlan::findOrFail($id);
        $data->delete();
    }

    /**
     * Get given clinic active plan
     */
    public function getActivePlan(string $clinic_id): ClinicHasPlan|null
    {
        return ClinicHasPlan::where('clinic_id', $clinic_id)
            ->where('active', true)
            ->first();
    }

    /**
     * Get given clinic active plans
     */
    public function getActivePlans(): Collection|null
    {
        return ClinicHasPlan::where('active', true)->get();
    }
}
