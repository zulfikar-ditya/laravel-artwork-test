<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\ClinicHasPlan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ClinicHasPlanServiceInterface extends BaseServiceInterface
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
     * Update service
     */
    public function update(FormRequest $request, ClinicHasPlan $ClinicHasPlan): void;

    /**
     * Delete service
     */
    public function delete(ClinicHasPlan $ClinicHasPlan): void;
}
