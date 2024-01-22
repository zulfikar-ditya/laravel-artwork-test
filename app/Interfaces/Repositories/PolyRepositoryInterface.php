<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Poly;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface PolyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Poly;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Poly;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
