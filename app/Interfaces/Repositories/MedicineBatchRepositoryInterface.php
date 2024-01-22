<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\MedicineBatch;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicineBatchRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * datatable repository is_merge
     */
    public function datatableIsMerge(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): MedicineBatch;

    /**
     * Show repository
     */
    public function show(string $id): MedicineBatch;

    /**
     * Update repository
     */
    public function update(array $data, string $id): MedicineBatch;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
