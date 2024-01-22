<?php

namespace App\Services;

use App\Interfaces\Repositories\MedicineRepositoryInterface;
use App\Interfaces\Services\MedicineServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Medicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MedicineService extends BaseService implements MedicineServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private MedicineRepositoryInterface $repository)
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
    public function update(FormRequest $request, Medicine $Medicine): void
    {
        $this->repository->update($request->validated(), $Medicine->id);
    }

    /**
     * Delete service
     */
    public function delete(Medicine $Medicine): void
    {
        $this->repository->delete($Medicine->id);
    }
}
