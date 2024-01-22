<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\Medicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicineServiceInterface extends BaseServiceInterface
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
    public function update(FormRequest $request, Medicine $Medicine): void;

    /**
     * Delete service
     */
    public function delete(Medicine $Medicine): void;
}
