<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\ArticleTag;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleTagRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): ArticleTag;

    /**
     * Update repository
     */
    public function update(array $data, string $id): ArticleTag;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
