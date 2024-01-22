<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ScheduleServiceInterface extends BaseServiceInterface
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
    public function show(Schedule $schedule): Schedule;

    /**
     * Update service
     */
    public function update(FormRequest $request, Schedule $schedule): void;

    /**
     * Delete service
     */
    public function delete(Schedule $schedule): void;
}
