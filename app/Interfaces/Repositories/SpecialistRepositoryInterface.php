<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface SpecialistRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Specialist;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Specialist;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
