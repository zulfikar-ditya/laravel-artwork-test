<?php

namespace App\Services;

use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Interfaces\Services\UserServiceInterface;
use App\Models\Clinic;
use App\Services\Base\BaseService;
use App\Models\User;
use App\Support\ClinicCache;
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
            ...$request->safe()->except('avatar', 'role', 'clinic_id', 'role_id', 'specialist_id'),
            'avatar' => $request->hasFile('avatar') ? $this->uploadFile($request->file('avatar'), 'avatar') : null
        ]);

        // * sync user role
        if ($request->safe()->has('role')) {
            $data->syncRoles($request->role);
        }

        // * sync user clinic
        if ($request->safe()->has('clinic_id') && $request->safe()->has('role_id')) {
            $arr = [];
            foreach ($request->clinic_id as $key => $clinic_id) {
                $arr[] = [
                    'clinic_id' => $clinic_id,
                    'role_id' => $request->role_id[$key]
                ];
            }

            $data->syncClinics($arr);
        }

        // * sync user specialist
        if ($request->safe()->has('specialist_id')) {
            $data->specialist()->sync($request->specialist_id);
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
            ...$request->safe()->except('avatar', 'role', 'clinic_id', 'role_id', 'specialist_id'),
            'avatar' => $newFile ?? $oldFile
        ], $User->id);

        // * sync user role
        if ($request->role) {
            $User->syncRoles($request->role);
        }

        // * delete old file
        if ($newFile) {
            $this->deleteFile($oldFile);
        }

        // * sync user clinic
        if ($request->safe()->has('clinic_id') && $request->safe()->has('role_id')) {
            $arr = [];
            foreach ($request->clinic_id as $key => $clinic_id) {
                $arr[] = [
                    'clinic_id' => $clinic_id,
                    'role_id' => $request->role_id[$key]
                ];
            }

            $User->syncClinics($arr);
        }

        // * sync user specialist
        if ($request->safe()->has('specialist_id')) {
            $User->specialist()->sync($request->specialist_id);
        }
    }

    /**
     * Delete service
     */
    public function delete(User $User): void
    {
        $oldFile = $User->avatar;
        $User->clinics()->detach();
        $User->specialist()->detach();
        $this->repository->delete($User->id);

        // * delete old file
        if ($oldFile) {
            $this->deleteFile($oldFile);
        }
    }

    /**
     * Detach user from a clinic
     */
    public function detachUserFromClinic(User $user, string $clinic): void
    {
        $user->clinics()->detach($clinic);

        // * delete user from clinic cache
        ClinicCache::deleteUserFromClinicCache($user);
    }
}
