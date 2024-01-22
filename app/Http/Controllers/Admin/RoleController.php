<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Interfaces\Services\RoleServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Instantiate a new Controllers instance.
     */
    public function __construct()
    {
        // $this->policyModel = App\Models\Role::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(RoleServiceInterface $roleService)
    {
        // $this->authorize('viewAny', App\Models\Role::class);

        return $this->responseJson($roleService->datatable(request()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRoleRequest $request)
    {
        // $this->authorize('create', App\Models\Role::class);

        DB::beginTransaction();

        try {
            $service = app(RoleServiceInterface::class);
            $service->create($request);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'create', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        // $this->authorize('view', $role);
        // service...

        return $this->responseJsonData($role);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRoleRequest $request, Role $role)
    {
        // $this->authorize('update', $role);

        DB::beginTransaction();

        try {
            $service = app(RoleServiceInterface::class);
            $service->update($request, $role);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'update', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // $this->authorize('delete', $role);

        DB::beginTransaction();

        try {
            $service = app(RoleServiceInterface::class);
            $service->delete($role);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return $this->responseJsonMessageCrud(false, 'delete', null, $th->getMessage(), 500);
        }

        DB::commit();

        return $this->responseJsonMessageCrud(true, 'delete');
    }
}
