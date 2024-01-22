<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Medicine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicineRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Medicine;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Medicine;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Get by ids repository
     */
    public function getByIds(array $ids): Collection;
}
