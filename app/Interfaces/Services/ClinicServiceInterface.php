<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\Clinic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClinicServiceInterface extends BaseServiceInterface
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
     * Show service
     */
    public function show(Clinic $Clinic): Clinic;

    /**
     * Update service
     */
    public function update(FormRequest $request, Clinic $Clinic): void;

    /**
     * Delete service
     */
    public function delete(Clinic $Clinic): void;

    /**
     * Get clinic ID
     */
    public function getClinicId(FormRequest $request): string|null;
}
