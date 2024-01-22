<?php

namespace App\Repositories;

use App\Enums\UserStatusEnum;
use App\Interfaces\Repositories\PatientRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    /**
     * Instantiate a new PatientRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            //
        ]);

        $this->setAllowableSort([
            //
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return Patient::query()
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $dataUser): Patient
    {
        $data =  Patient::create($dataUser);

        // try find user by email
        $userRepository = new UserRepository();
        $user = $userRepository->findByEmail($data->email);

        if ($user && $data->user_id != $user->id) {
            $data->user_id = $user->id;
            $data->save();
        }

        // create new user
        if (is_null($user)) {
            $userRepository = new UserRepository();
            $userRepository->create([
                ...$dataUser,
                'password' => 'password@' . $data['name'],
                'status' => UserStatusEnum::INACTIVE,
            ]);
        }

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Patient
    {
        $model = Patient::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Patient::findOrFail($id);
        $data->delete();
    }

    /**
     * find patient by user
     */
    public function findByUser(User $user, string $type = 'all'): Patient|null
    {
        if ($type == 'all') {
            if (!is_null($patient = Patient::where('user_id', $user->id)->first())) {
                return $patient;
            }

            return Patient::where('email', $user->email)->first();
        }

        if ($type == 'email') {
            return Patient::where('email', $user->email)->first();
        }

        return Patient::where('user_id', $user->id)->first();
    }

    /**
     * find patient form request
     */
    public function findByRequest(FormRequest $formRequest): Patient|null
    {
        $patientId = $formRequest->safe()->only('patient_id')['patient_id'] ?? null;
        $patientEmail = $formRequest->safe()->only('patient_email')['patient_email'] ?? null;

        if (!is_null($patientId)) {
            return $this->findPatientById($patientId);
        }

        if (!is_null($patientEmail)) {
            if (!is_null($patient = $this->findPatientByEmail($patientEmail))) {
                return $patient;
            }
        }

        $formRequestSafe = $formRequest->safe()->only([
            'patient_name',
            'patient_email',
            'patient_gender',
            'patient_phone_number',
            'patient_address',
            'patient_date_of_birth',
            'height',
            'weight',
        ]);

        return $this->createNewPatient($formRequestSafe);
    }

    /**
     * Find patient by ID
     */
    public function findPatientById(string $patientId): Patient|null
    {
        return Patient::find($patientId);
    }

    /**
     * Find patient by email
     */
    public function findPatientByEmail(string $patientEmail): Patient|null
    {
        return Patient::where('email', $patientEmail)->first();
    }

    /**
     * Create new patient
     */
    private function createNewPatient(array $data): Patient
    {
        return $this->create([
            'name' => $data['patient_name'] ?? null,
            'email' => $data['patient_email'] ?? null,
            'gender' => $data['patient_gender'] ?? null,
            'phone_number' => $data['patient_phone_number'] ?? null,
            'address' => $data['patient_address'] ?? null,
            'date_of_birth' => $data['patient_date_of_birth'] ?? null,
            'height' => $data['height'] ?? null,
            'weight' => $data['weight'] ?? null,
        ]);
    }
}
