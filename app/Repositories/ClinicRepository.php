<?php

namespace App\Repositories;

use App\Enums\ClinicStatusEnum;
use App\Interfaces\Repositories\ClinicRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Clinic;
use App\Models\ClinicAttachment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use stdClass;

class ClinicRepository extends BaseRepository implements ClinicRepositoryInterface
{
    /**
     * Instantiate a new ClinicRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'sort',
            'name',
            'address',
            'phone',
            'status',
        ]);

        $this->setAllowableSort([
            'sort',
            'name',
            'address',
            'phone',
            'status',
            'created_at',
            'updated_at',
        ]);

        $this->setAllowableInclude([
            'createdId' => function ($q) {
                $q->select([
                    'id',
                    'name',
                    'email',
                ]);
            },
            'clinicAttachments' => function ($q) {
                $q->select([
                    'id',
                    'clinic_id',
                    'name',
                    'path',
                ]);
            },
            'clinicPolies'
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Clinic::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Clinic
    {
        $data =  Clinic::create($data);

        return $data;
    }

    /**
     * Show repository
     */
    public function show(string $id): Clinic
    {
        $data = Clinic::with($this->getAllowableInclude())->findOrFail($id);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Clinic
    {
        $model = Clinic::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Clinic::findOrFail($id);
        $data->delete();
    }

    /**
     * Get clinic attachments from given ids
     */
    public function getAttachments(Clinic $clinic, array $ids): Collection
    {
        return ClinicAttachment::whereIn('id', $ids)->where('clinic_id', $clinic->id)->get();
    }

    /**
     * Get available user clinic
     */
    public function getAvailableClinic(string $clinicId = null, string $roleName = null): SupportCollection|null
    {
        return DB::table('user_has_clinics')
            ->join('clinics', function ($q) use ($clinicId) {
                $q->on('clinics.id', '=', 'user_has_clinics.clinic_id')
                    ->when($clinicId, function ($q) use ($clinicId) {
                        $q->where('clinics.id', $clinicId);
                    });
            })
            ->join('roles', function ($q) use ($roleName) {
                $q->on('roles.id', '=', 'user_has_clinics.role_id')
                    ->when($roleName, function ($q) use ($roleName) {
                        $q->where('roles.name', $roleName);
                    });
            })
            ->where('user_has_clinics.user_id', Auth::user()->id)
            ->select([
                'clinics.id',
                'clinics.sort',
                'clinics.name',
                'roles.name as role_name',
            ])
            ->get();
    }

    /**
     * Get user clinic
     */
    public function getUserClinic(?string $id, string $roleName = null): SupportCollection|null|stdClass
    {
        if ($id == null) {
            return null;
        }

        return DB::table('user_has_clinics')
            ->join('clinics', function ($q) {
                $q->on('clinics.id', '=', 'user_has_clinics.clinic_id');
            })
            ->join('roles', function ($q) use ($roleName) {
                $q->on('roles.id', '=', 'user_has_clinics.role_id')
                    ->when($roleName, function ($q) use ($roleName) {
                        $q->where('roles.name', $roleName);
                    });
            })
            ->where('user_id', Auth::user()->id)
            ->where('clinic_id', $id)
            ->select([
                'clinics.id',
                'clinics.sort',
                'clinics.name',
                'roles.name as role_name',
            ])
            ->first();
    }

    /**
     * Get user all clinic
     */
    public function getAllUserClinic(?string $id, string $roleName = null): SupportCollection
    {
        if ($id == null) {
            return null;
        }

        return DB::table('user_has_clinics')
            ->join('clinics', function ($q) {
                $q->on('clinics.id', '=', 'user_has_clinics.clinic_id');
            })
            ->join('roles', function ($q) use ($roleName) {
                $q->on('roles.id', '=', 'user_has_clinics.role_id')
                    ->when($roleName, function ($q) use ($roleName) {
                        $q->where('roles.name', $roleName);
                    });
            })
            ->where('clinic_id', $id)
            ->select([
                'clinics.id',
                'clinics.sort',
                'clinics.name',
                'user_has_clinics.user_id',
                'roles.name as role_name',
            ])
            ->get();
    }

    /**
     * is user in clinic
     */
    public function isUserInClinic(string $clinicId, string $userId): bool
    {
        return DB::table('user_has_clinics')
            ->where('clinic_id', $clinicId)
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Get list of clinic
     */
    public function getList(Request $request): LengthAwarePaginator
    {
        return Clinic::query()
            ->withoutGlobalScope(\App\Models\Scopes\CurrentClinicScope::class)
            ->with([
                'clinicPolies' => function ($q) {
                    $q->select([
                        'id',
                        'name',
                    ]);
                },
            ])
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->polies && is_array($request->polies), function ($q) use ($request) {
                $q->whereHas('clinicPolies', function ($q) use ($request) {
                    $q->whereIn('poly_id', $request->polies);
                });
            })
            ->where('status', ClinicStatusEnum::ACTIVE)
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->select([
                'id',
                'created_id',
                'sort',
                'name',
                'slug',
                'address',
                'phone',
                'image',
            ])
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Show clinic by slug
     */
    public function showBySlug(string $slug): Clinic
    {
        $with = $this->getAllowableInclude();
        array_push($with, 'clinicPolies');

        $data = Clinic::with($this->getAllowableInclude())->where('slug', $slug)->firstOrFail();
        $data->doctors = $this->getUserAssociated($data->id, 'doctor');

        return $data;
    }

    /**
     * Get user associated with this clinics.
     */
    public function getUserAssociated(string $slug, string $roleName): LengthAwarePaginator
    {
        return DB::table('user_has_clinics')
            ->join('users', function ($q) {
                $q->on('users.id', '=', 'user_has_clinics.user_id');
            })
            ->join('roles', function ($q) use ($roleName) {
                $q->on('roles.id', '=', 'user_has_clinics.role_id')
                    ->where('roles.name', $roleName);
            })
            ->join('clinics', function ($q) use ($slug) {
                $q->on('clinics.id', '=', 'user_has_clinics.clinic_id')
                    ->where('clinics.slug', $slug);
            })
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.gender',
                'users.phone_number',
                'users.address',
                'users.avatar',
                'roles.name as role_name',
            ])
            ->paginate(10);
    }

    /**
     * Get count of given clinic medical record
     */
    public function getMedicalRecordCount(string $clinic_id): int
    {
        return DB::table('medical_records')
            ->where('clinic_id', $clinic_id)
            ->count();
    }
}
