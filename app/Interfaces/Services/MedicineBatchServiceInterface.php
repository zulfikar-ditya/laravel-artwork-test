<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\MedicineBatch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MedicineBatchServiceInterface extends BaseServiceInterface
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
    public function update(FormRequest $request, MedicineBatch $MedicineBatch): void;

    /**
     * Delete service
     */
    public function delete(MedicineBatch $MedicineBatch): void;
}
