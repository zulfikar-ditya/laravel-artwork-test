<?php

namespace App\Repositories;

use App\Interfaces\Repositories\MedicalRecordRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\MedicalRecord;
use App\Models\MedicalRecordAttachment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MedicalRecordRepository extends BaseRepository implements MedicalRecordRepositoryInterface
{
    /**
     * Instantiate a new MedicalRecordRepository instance.
     */
    public function __construct()
    {

        $this->setAllowableFilter([
            'clinic',
            'patient',
            'doctor',
            'poly',
        ]);

        $this->setAllowableSearch([
            'clinic.name',
            'patient.name',
            'doctor.name',
            'poly.name',
            'code',
            'date',
            // 'diagnosis',
            // 'therapy',
            // 'handling',
            // 'description',
            'status',
        ]);

        $this->setAllowableSort([
            'clinic.name',
            'patient.name',
            'doctor.name',
            'poly.name',
            'code',
            'date',
            // 'diagnosis',
            // 'therapy',
            // 'handling',
            // 'description',
            'status',
        ]);

        $this->setAllowableFilter([
            'clinic.name',
            'patient.name',
            'doctor.name',
            'poly.name',
            'code',
            'date',
            // 'diagnosis',
            // 'therapy',
            // 'handling',
            // 'description',
            'status',
        ]);

        $this->setAllowableInclude([
            'clinic' => function ($q) {
                return $q->select([
                    'id',
                    'sort',
                    'name',
                ]);
            },
            'patient',
            'doctor' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                    'email',
                ]);
            },
            'poly' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                ]);
            },
        ]);
    }

    /**
     * datatable repository query
     */
    private function datatableQuery(Request $request): Builder
    {
        return MedicalRecord::query()
            ->with($this->getAllowableInclude())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when($request->filter, function ($query, $filter) {
                $this->getAllowableFilterQuery($query, $filter);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection());
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this->datatableQuery($request)
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository for current user
     */
    public function datatableCurrentUser(Request $request): LengthAwarePaginator
    {
        $user = Auth::user();
        $patientRepository = new PatientRepository();
        $patient = $patientRepository->findByUser($user);

        return $this->datatableQuery($request)
            ->where('patient_id', $patient->id)
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): MedicalRecord
    {
        $data =  MedicalRecord::create($data);

        return $data;
    }

    /**
     * show repository
     */
    public function show(string $id): MedicalRecord
    {
        $with = $this->getAllowableInclude();
        $with['medicalRecordAttachments'] = function ($q) {
            return $q->select([
                'id',
                'medical_record_id',
                'name',
                'path'
            ]);
        };
        $with['medicalRecordMedicines'] = function ($q) {
            return $q->select([
                'id',
                'medical_record_id',
                'medicine_id',
                'dosage_form',
                'quantity',
                'description',
            ]);
        };
        $with['medicalRecordMedicines.medicine'] = function ($q) {
            return $q->select([
                'id',
                'name',
            ]);
        };

        return MedicalRecord::with($with)
            ->findOrFail($id);
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): MedicalRecord
    {
        $model = MedicalRecord::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = MedicalRecord::findOrFail($id);
        $data->delete();
    }

    /**
     * Get will deleted attachments.
     */
    public function getWillDeletedAttachments(MedicalRecord $medicalRecord, array $ids): array
    {
        return MedicalRecordAttachment::where('medical_record_id', $medicalRecord->id)
            ->whereIn('id', $ids)
            ->pluck('path')
            ->toArray();
    }

    /**
     * Delete will deleted attachments
     */
    public function deleteWillDeletedAttachments(MedicalRecord $medicalRecord, array $ids): void
    {
        MedicalRecordAttachment::where('medical_record_id', $medicalRecord->id)
            ->whereIn('id', $ids)
            ->delete();
    }
}
