<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\MedicalRecord;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicalRecordServiceInterface extends BaseServiceInterface
{
    /**
     * Datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create service
     */
    public function create(FormRequest $request): void;

    /**
     * show service
     */
    public function show(MedicalRecord $MedicalRecord): MedicalRecord;

    /**
     * Update service
     */
    public function update(FormRequest $request, MedicalRecord $MedicalRecord): void;

    /**
     * Delete service
     */
    public function delete(MedicalRecord $MedicalRecord): void;
}
