<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\MergeMedicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface MergeMedicineServiceInterface extends BaseServiceInterface
{
    /**
     * Datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create service
     */
    public function create(FormRequest $request): void;

    // /**
    //  * Update service
    //  */
    // public function update(FormRequest $request, MergeMedicine $MergeMedicine): void;

    // /**
    //  * Delete service
    //  */
    // public function delete(MergeMedicine $MergeMedicine): void;
}
