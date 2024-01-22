<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface ScheduleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator;

    /**
     * Create repository
     */
    public function create(array $data): Schedule;

    /**
     * Show repository
     */
    public function show(string $id): Schedule;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Schedule;

    /**
     * Delete repository
     */
    public function delete(string $id): void;

    /**
     * Select option for selecting the schedules.
     */
    public function selectOption(Request $request): array;

    /**
     * Get schedules by user_id
     */
    public function getByUser(string $doctorId): Collection;
}
