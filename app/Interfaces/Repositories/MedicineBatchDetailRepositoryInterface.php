<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\MedicineBatchDetail;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicineBatchDetailRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable stock mutations
     */
    public function datatableStockMutations(Request $request): LengthAwarePaginator;

    /**
     * Datatable stock summary
     */
    public function datatableStockSummary(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): MedicineBatchDetail;

    /**
     * Update repository
     */
    public function update(array $data, string $id): MedicineBatchDetail;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Get stock left each item.
     */
    public function getStockLeftEachItem(string $medicineId): int;

    /**
     * Get stock left medicines
     */
    public function getStockLeftMedicines(array $medicineIds);
}
