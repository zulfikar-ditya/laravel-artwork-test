<?php

namespace App\Services;

use App\Exceptions\ClinicCacheNotFoundException;
use App\Exceptions\InvalidUserRoleException;
use App\Interfaces\Repositories\ScheduleRepositoryInterface;
use App\Interfaces\Services\ScheduleServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Schedule;
use App\Models\User;
use App\Support\ClinicCache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ScheduleService extends BaseService implements ScheduleServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private ScheduleRepositoryInterface $repository)
    {
        //
    }

    /**
     * Datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        return $this->repository->datatable($request);
    }

    /**
     * Create service
     */
    public function create(FormRequest $request): void
    {
        if (!Auth::check()) {
            abort(401);
        }

        // * get clinic
        $clinic = $request->clinic_id;
        if (is_null($clinic)) {
            $clinic = ClinicCache::getCurrentClinic()->id;
        }

        if (is_null($clinic)) {
            throw new ClinicCacheNotFoundException();
        }

        // * get the user
        $user_id = $request->user_id;
        if (is_null($user_id)) {
            throw new ModelNotFoundException();
        }

        $user = User::find($user_id);

        // * get the user
        // * determine if the user_id has role doctor
        if (!$user->hasRole('doctor')) {
            throw new InvalidUserRoleException('doctor');
        }

        // if the user want to create many schedules.
        // loop trough the days and create a schedule for each day.
        if (is_array($request->days)) {
            foreach ($request->days as $day) {
                $this->repository->create([
                    ...$request->validated(),
                    'clinic_id' => $clinic,
                    'user_id' => $user->id,
                    'days' => $day,
                    'from' => Carbon::parse($request->from)->format('H:i'),
                    'to' => Carbon::parse($request->to)->format('H:i'),
                ]);
            }

            return;
        }

        // if the days request is not array create single schedule
        $this->repository->create([
            ...$request->validated(),
            'clinic_id' => $clinic,
            'user_id' => $user->id,
            'from' => Carbon::parse($request->from)->format('H:i'),
            'to' => Carbon::parse($request->to)->format('H:i'),
        ]);
    }

    /**
     * Show service
     */
    public function show(Schedule $Schedule): Schedule
    {
        return $this->repository->show($Schedule->id);
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, Schedule $Schedule): void
    {
        $this->repository->update($request->validated(), $Schedule->id);
    }

    /**
     * Delete service
     */
    public function delete(Schedule $Schedule): void
    {
        $this->repository->delete($Schedule->id);
    }
}
