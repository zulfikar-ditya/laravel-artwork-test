<?php

namespace App\Repositories;

use App\Exceptions\ClinicCacheNotFoundException;
use App\Exceptions\PasswordOldNotMatchException;
use App\Interfaces\Repositories\ClinicRepositoryInterface;
use App\Interfaces\Repositories\UserRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\User;
use App\Support\ClinicCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * Instantiate a new UserRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'name',
            'email',
            'password',
            'phone_number',
            'address',
            // 'avatar',
            'status',
            'gender',
        ]);

        $this->setAllowableSort([
            'name',
            'email',
            'password',
            'phone_number',
            'address',
            // 'avatar',
            'status',
            'gender',
        ]);

        $this->setAllowableFilter([
            'status',
            'gender',
        ]);

        $this->setAllowableInclude([
            'clinics' => function ($q) {
                $q->select([
                    'clinics.id',
                    'name',
                    'sort'
                ]);
            },
            'roles' => function ($q) {
                $q->select([
                    'roles.id',
                    'name',
                ]);
            },
            'specialist' => function ($q) {
                $q->select([
                    'specialists.id',
                    'name',
                ]);
            },
        ]);
    }

    /**
     * datatable query repository
     */
    private function datatableQuery(Request $request): Builder
    {
        return User::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->filter && is_array($request->filter), function ($query) use ($request) {
                $this->getAllowableFilterQuery($query, $request->filter);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection());
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this->datatableQuery($request)->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository current cache clinic
     */
    public function datatableCurrentCacheClinic(Request $request): LengthAwarePaginator
    {
        $currentClinic = app(ClinicRepositoryInterface::class)->getUserClinic(ClinicCache::getCurrentClinic()->id);

        // if the current clinic is null set to available
        if (!$currentClinic) {
            throw new ClinicCacheNotFoundException();
        }

        return $this->datatableQuery($request)
            ->whereHas('clinics', function ($query) use ($currentClinic) {
                $query->where('clinics.id', $currentClinic->id);
            })
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository current cache clinic for given roles
     */
    public function datatableCurrentCacheClinicForGivenRoles(Request $request, array $roles, bool $withClinicCache = true): LengthAwarePaginator
    {
        $currentClinic = null;
        if ($withClinicCache) {
            $currentClinic = app(ClinicRepositoryInterface::class)->getUserClinic(ClinicCache::getCurrentClinic()->id);

            if (!$currentClinic) {
                throw new ClinicCacheNotFoundException();
            }
        }

        return $this->datatableQuery($request)
            ->when($currentClinic && $withClinicCache, function ($query) use ($currentClinic) {
                $query->whereHas('clinics', function ($query) use ($currentClinic) {
                    $query->where('clinics.id', $currentClinic->id);
                });
            })
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository for patient
     */
    public function datatablePatient(Request $request): LengthAwarePaginator
    {
        return $this->datatableQuery($request)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'patient');
            })
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): User
    {
        $data =  User::create($data);

        return $data;
    }

    /**
     * show repository
     */
    public function show(string $id): User
    {
        $data = User::with($this->getAllowableInclude())->findOrFail($id);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): User
    {
        $model = User::findOrFail($id);

        // if array contains old password
        if (isset($data['old_password'])) {
            // check if old password is correct
            if (!Hash::check($data['old_password'], $model->password)) {
                throw new PasswordOldNotMatchException();
            }

            // unset old password
            unset($data['old_password']);
        }

        $model->update($data);


        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = User::findOrFail($id);
        $data->delete();
    }

    /**
     * Find user by email address
     */
    public function findByEmail(string $email): User|null
    {
        return User::where('email', $email)->first();
    }
}
