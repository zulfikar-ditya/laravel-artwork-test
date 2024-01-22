<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Base\BaseRepositoryInterface;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingRepositoryInterface extends BaseRepositoryInterface
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
    public function create(array $data): Booking;

    /**
     * Update repository
     */
    public function update(array $data, string $id): Booking;

    /**
     * Delete repository
     */
    public function delete(string $id): void;
}
