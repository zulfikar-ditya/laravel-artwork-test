<?php

namespace App\Services;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\UserServiceInterface;
use App\Services\Base\BaseService;
use App\Models\User;
use App\Traits\UploadFile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService extends BaseService implements UserServiceInterface
{
    use UploadFile;

    /**
     * The constructor service
     */
    public function __construct(private UserRepositoryInterface $repository)
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
        $data = $this->repository->create([
            ...$request->safe()->except('avatar', 'role_id'),
            'avatar' => $request->hasFile('avatar') ? $this->uploadFile($request->file('avatar'), 'avatar') : null
        ]);

        // * sync user role
        if ($request->safe()->has('role_id')) {
            $data->roles()->sync($request->role_id);
        }
    }

    /**
     * show service
     */
    public function show(User $User): User
    {
        return $this->repository->show($User->id);
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, User $User): void
    {
        $oldFile = $User->avatar;
        $newFile = null;

        // * check if has file
        if ($request->hasFile('avatar')) {
            $newFile = $this->uploadFile($request->file('avatar'), 'avatar');
        }

        $this->repository->update([
            ...$request->safe()->except('avatar', 'role_id'),
            'avatar' => $newFile ?? $oldFile
        ], $User->id);

        // * sync user role
        if ($request->safe()->has('role_id')) {
            $User->roles()->sync($request->role_id);
        }

        // * delete old file
        if ($newFile) {
            $this->deleteFile($oldFile);
        }
    }

    /**
     * Delete service
     */
    public function delete(User $User): void
    {
        $oldFile = $User->avatar;
        $this->repository->delete($User->id);

        // * delete old file
        if ($oldFile) {
            $this->deleteFile($oldFile);
        }
    }
}
