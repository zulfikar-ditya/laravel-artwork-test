<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Patient;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Patient;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * find patient by user
     */
    public function findByUser(User $user, string $type = 'all'): Patient|null;

    /**
     * find patient form request
     */
    public function findByRequest(FormRequest $formRequest): Patient|null;

    /**
     * Find patient by ID
     */
    public function findPatientById(string $patientId): Patient|null;

    /**
     * Find patient by email
     */
    public function findPatientByEmail(string $patientEmail): Patient|null;
}
