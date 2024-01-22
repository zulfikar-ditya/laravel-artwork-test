<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Article;

    /**
     * show repository
     */
    public function show(string $id): Article;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Article;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Get list of articles
     */
    public function getListArticles(Request $request): LengthAwarePaginator;

    /**
     * Show repository for client
     */
    public function getArticleBySlug(string $slug): Article;
}
