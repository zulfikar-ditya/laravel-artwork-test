<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleServiceInterface extends BaseServiceInterface
{
    /**
     * Datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create service
     */
    public function create(FormRequest $request): void;

    /**
     * show service
     */
    public function show(Article $Article): Article;

    /**
     * Update service
     */
    public function update(FormRequest $request, Article $Article): void;

    /**
     * Delete service
     */
    public function delete(Article $Article): void;
}
