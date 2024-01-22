<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;
use stdClass;

interface ClinicRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Clinic;

    /**
     * Show repository
     */
    public function show(string $id): Clinic;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Clinic;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Get clinic attachments from given ids
     */
    public function getAttachments(Clinic $clinic, array $ids): Collection;

    /**
     * Get available user clinic
     */
    public function getAvailableClinic(string $clinicId = null, string $roleName = null): SupportCollection|null;

    /**
     * Get user clinic
     */
    public function getUserClinic(?string $id, string $roleName = null): SupportCollection|null|stdClass;

    /**
     * Get user clinic
     */
    public function getAllUserClinic(?string $id, string $roleName = null): SupportCollection;

    /**
     * is user in clinic
     */
    public function isUserInClinic(string $clinicId, string $userId): bool;

    /**
     * Get list of clinic
     */
    public function getList(Request $request): LengthAwarePaginator;

    /**
     * Show clinic by slug
     */
    public function showBySlug(string $slug): Clinic;

    /**
     * Get user associated with this clinics.
     */
    public function getUserAssociated(string $clinicId, string $roleName): LengthAwarePaginator;

    /**
     * Get count of given clinic medical record
     */
    public function getMedicalRecordCount(string $clinic_id): int;
}
