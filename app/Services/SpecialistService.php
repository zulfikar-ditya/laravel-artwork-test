<?php

namespace App\Services;

use App\Interfaces\Repositories\SpecialistRepositoryInterface;
use App\Interfaces\Services\SpecialistServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Specialist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class SpecialistService extends BaseService implements SpecialistServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private SpecialistRepositoryInterface $repository)
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
    public function update(FormRequest $request, Specialist $specialist): void
    {
        $this->repository->update($request->validated(), $specialist->id);
    }

    /**
     * Delete service
     */
    public function delete(Specialist $specialist): void
    {
        $this->repository->delete($specialist->id);
    }
}
