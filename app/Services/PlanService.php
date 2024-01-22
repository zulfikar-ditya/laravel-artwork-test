<?php

namespace App\Services;

use App\Interfaces\Repositories\PlanRepositoryInterface;
use App\Interfaces\Services\PlanServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PlanService extends BaseService implements PlanServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private PlanRepositoryInterface $repository)
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
    public function update(FormRequest $request, Plan $Plan): void
    {
        $this->repository->update($request->validated(), $Plan->id);
    }

    /**
     * Delete service
     */
    public function delete(Plan $Plan): void
    {
        $this->repository->delete($Plan->id);
    }
}
