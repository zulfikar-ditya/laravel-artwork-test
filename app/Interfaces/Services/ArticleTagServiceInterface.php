<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\ArticleTag;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface ArticleTagServiceInterface extends BaseServiceInterface
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
     * Update service
     */
    public function update(FormRequest $request, ArticleTag $ArticleTag): void;

    /**
     * Delete service
     */
    public function delete(ArticleTag $ArticleTag): void;
}
