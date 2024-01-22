<?php

namespace App\Services;

use App\Interfaces\Repositories\ArticleTagRepositoryInterface;
use App\Interfaces\Services\ArticleTagServiceInterface;
use App\Services\Base\BaseService;
use App\Models\ArticleTag;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleTagService extends BaseService implements ArticleTagServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private ArticleTagRepositoryInterface $repository)
    {
        //
    }

    /**
     * Datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this->repository->datatable($request);
    }

    /**
     * Create service
     */
    public function create(FormRequest $request): void
    {
        $this->repository->create($request->validated());
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, ArticleTag $ArticleTag): void
    {
        $this->repository->update($request->validated(), $ArticleTag->id);
    }

    /**
     * Delete service
     */
    public function delete(ArticleTag $ArticleTag): void
    {
        $this->repository->delete($ArticleTag->id);
    }
}
