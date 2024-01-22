<?php

namespace App\Services;

use App\Interfaces\Repositories\RoleRepositoryInterface;
use App\Interfaces\Services\RoleServiceInterface;
use App\Models\Role;
use App\Services\Base\BaseService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService extends BaseService implements RoleServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private RoleRepositoryInterface $repository)
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
    public function update(FormRequest $request, Role $Role): void
    {
        $this->repository->update($request->validated(), $Role->id);
    }

    /**
     * Delete service
     */
    public function delete(Role $Role): void
    {
        $this->repository->delete($Role->id);
    }
}
