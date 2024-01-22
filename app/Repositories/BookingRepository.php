<?php

namespace App\Repositories;

use App\Enums\BookingStatusEnum;
use App\Interfaces\Repositories\BookingRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class BookingRepository extends BaseRepository implements BookingRepositoryInterface
{
    /**
     * Instantiate a new BookingRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'schedule.days',
            'doctor.name',
            'doctor.email',
            'user.name',
            'user.email',
            'createdId.name',
            'createdId.email',
            'clinic.name',
            'clinic.sort',
            'date',
            'code',
            // 'from',
            // 'to',
            'status',
            // 'description',
        ]);

        $this->setAllowableSort([
            'schedule_id',
            'doctor_id',
            'user_id',
            'created_id',
            'clinic_id',
            'date',
            'code',
            'from',
            'to',
            'status',
            'description',
            'created_at',
            'updated_at',
        ]);

        $this->setAllowableFilter([
            'clinic_id',
            'doctor_id',
            'user_id',
            'created_id',
            'date',
            'created_at',
            'status',
        ]);

        $this->setAllowableInclude([
            'schedule' => function ($q) {
                return $q->select([
                    'id',
                    'days',
                    'from',
                    'to'
                ]);
            },
            'clinic' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                    'sort'
                ]);
            },
            'doctor' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                    'email'
                ]);
            },
            'user' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                    'email'
                ]);
            },
            'createdId' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                    'email'
                ]);
            },
        ]);
    }

    /**
     * datatable repository query
     */
    private function datatableQuery(Request $request): Builder
    {
        return Booking::query()
            ->with($this->getAllowableInclude())
            ->when($request->search, function ($query, $search) {
                $this->getAllowableSearchQuery($query, $search);
            })
            ->when(is_array($request->filter), function ($query) use ($request) {
                $this->getAllowableFilterQuery($query, $request->filter);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection());
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this
            ->datatableQuery($request)
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * datatable repository for current user
     */
    public function datatableCurrentUser(Request $request): LengthAwarePaginator
    {
        return $this
            ->datatableQuery($request)
            ->where('user_id', Auth::id())
            ->paginate($request->per_page ?? config('app.default_paginator'));
    }

    /**
     * Create repository
     */
    public function create(array $data): Booking
    {
        $data =  Booking::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Booking
    {
        $model = Booking::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Booking::findOrFail($id);
        $data->delete();
    }

    /**
     * Get bookings by date and doctor
     */
    public function getByDateAndDoctor(Carbon $date, string $doctorId): Collection
    {
        return Booking::whereDate('date', $date)
            ->where('doctor_id', $doctorId)
            ->whereNotIn('status', [
                BookingStatusEnum::REJECTED,
                BookingStatusEnum::CANCEL,
                BookingStatusEnum::COMPLETE,
                BookingStatusEnum::EXPIRED,
            ])
            ->get();
    }
}
