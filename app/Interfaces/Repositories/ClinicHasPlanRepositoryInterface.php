<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\ClinicHasPlan;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClinicHasPlanRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): ClinicHasPlan;

    /**
     * Show repository
     */
    public function show(string $id): ClinicHasPlan;

    /**
     * Update repository
     */
    public function update(array $data, string $id): ClinicHasPlan;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Get given clinic active plan
     */
    public function getActivePlan(string $clinic_id): ClinicHasPlan|null;

    /**
     * Get given clinic active plans
     */
    public function getActivePlans(): Collection|null;
}
