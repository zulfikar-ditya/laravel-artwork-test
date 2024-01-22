<?php

namespace App\Services;

use App\Interfaces\Repositories\PolyRepositoryInterface;
use App\Interfaces\Services\PolyServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Poly;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PolyService extends BaseService implements PolyServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private PolyRepositoryInterface $repository)
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
    public function update(FormRequest $request, Poly $Poly): void
    {
        $this->repository->update($request->validated(), $Poly->id);
    }

    /**
     * Delete service
     */
    public function delete(Poly $Poly): void
    {
        $this->repository->delete($Poly->id);
    }
}
