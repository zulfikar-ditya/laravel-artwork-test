<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ScheduleRepositoryInterface;
use App\Models\Role;
use App\Models\Schedule;
use App\Repositories\Base\BaseRepository;
use App\Support\ClinicCache;
use App\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ScheduleRepository extends BaseRepository implements ScheduleRepositoryInterface
{
    /**
     * Instantiate a new ScheduleRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            'days',
            'from',
            'to',
            'active'
        ]);

        $this->setAllowableSort([
            'days',
            'from',
            'to',
            'active'
        ]);

        $this->setAllowableInclude([
            'clinic' => function ($q) {
                return $q->select([
                    'id',
                    'sort',
                    'name',
                    'slug',
                ]);
            },
            'user' => function ($q) {
                return $q->select([
                    'id',
                    'name',
                    'email',
                ]);
            },
        ]);
    }

    /**
     * datatable repository
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return \App\Models\User::whereHas('clinics', function ($q) {
            return $q->where('clinic_id', ClinicCache::getCurrentClinic()->id)->where('role_id', Role::where('name', 'doctor')->first()->id);
        })
            ->with('schedules', function ($q) {
                return $q->where('clinic_id', ClinicCache::getCurrentClinic()->id);
            })
            ->orderBy($this->getSortColumn(), $this->getSortDirection())
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.avatar',
            ])
            ->paginate($request->per_page ?? 10);
    }

    /**
     * Create repository
     */
    public function create(array $data): Schedule
    {
        $data =  Schedule::create($data);

        return $data;
    }

    /**
     * Show repository
     */
    public function show(string $id): Schedule
    {
        return Schedule::with($this->getAllowableInclude())
            ->findOrFail($id);
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): Schedule
    {
        $model = Schedule::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = Schedule::findOrFail($id);
        $data->delete();
    }

    /**
     * Select option for selecting the schedules.
     */
    public function selectOption(Request $request): array
    {
        // get the date and day from request
        $date = Carbon::parse($request->date);
        $day = Str::lower(Carbon::parse($request->date)->format('l'));
        $userId = $request->doctor_id;

        if (!Str::isUuid($userId)) {
            throw new \InvalidArgumentException("The given doctor id is not a valid. the doctor id must be uuid.");
        }

        // get bookings by date and doctor
        $bookings = (new BookingRepository())->getByDateAndDoctor($date, $userId);

        $BookingFrom = $bookings->pluck('from')->toArray();
        $BookingTo = $bookings->pluck('to')->toArray();

        // Format each time value in $BookingFrom
        $formattedFromTimes = array_map(function ($time) {
            return Carbon::parse($time)->format('H:i');
        }, $BookingFrom);

        // Format each time value in $BookingTo
        $formattedToTimes = array_map(function ($time) {
            return Carbon::parse($time)->format('H:i');
        }, $BookingTo);

        // get the schedule by day and doctor
        $schedule = Schedule::where('user_id', $userId)
            ->where('days', "{$day}")
            ->where('active', true)
            ->firstOrFail();

        $schedulesFrom = Carbon::parse($schedule->from);
        $schedulesTo = Carbon::parse($schedule->to);

        // !
        // distinct in hour (from, from and to columns)
        // then split to each hour
        // then filter by from and to
        // push to collection
        $schedules = collect();
        $distinct = $schedulesFrom->diffInHours($schedulesTo);
        for ($i = 0; $i <= $distinct; $i++) {
            $from = $schedulesFrom->format('H:i');
            $to = $schedulesFrom->addHour()->format('H:i');

            $schedules->push([
                'from' => $from,
                'to' => $to,
                'active' => (!in_array($from, $formattedFromTimes) && !in_array($to, $formattedToTimes)) ? true : false
            ]);
        }

        // return the schedules and available hours
        return [
            'schedule' => $schedule,
            'available_hours' => $schedules
        ];
    }

    /**
     * Get schedules by user_id
     */
    public function getByUser(string $doctorId): Collection
    {
        return Schedule::where('user_id', $doctorId)
            ->get();
    }
}
