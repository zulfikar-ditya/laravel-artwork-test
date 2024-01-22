<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface PlanRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Plan;

    /**
     * show repository
     */
    public function show(string $id): Plan;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Plan;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
