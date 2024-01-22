<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicalRecordRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * datatable repository for current user
     */
    public function datatableCurrentUser(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): MedicalRecord;

    /**
     * Show repository
     */
    public function show(string $id): MedicalRecord;

    /**
     * Update repository
     */
    public function update(array $data, string $id): MedicalRecord;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Get will deleted attachments.
     */
    public function getWillDeletedAttachments(MedicalRecord $medicalRecord, array $ids): array;

    /**
     * Delete will deleted attachments
     */
    public function deleteWillDeletedAttachments(MedicalRecord $medicalRecord, array $ids): void;
}
