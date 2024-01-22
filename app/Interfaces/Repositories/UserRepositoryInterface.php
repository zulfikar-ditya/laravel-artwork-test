<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): User;

    /**
     * show repository
     */
    public function show(string $id): User;

    /**
     * Update repository
     */
    public function update(array $data, string $id): User;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
