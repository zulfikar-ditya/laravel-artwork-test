<?php

namespace App\Services;

use App\Interfaces\Repositories\BookingRepositoryInterface;
use App\Interfaces\Repositories\ClinicRepositoryInterface;
use App\Interfaces\Services\BookingServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Booking;
use App\Models\Clinic;
use App\Repositories\UserRepository;
use App\Support\ClinicCache;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingService extends BaseService implements BookingServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private BookingRepositoryInterface $repository)
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
     * Create a new booking.
     */
    public function create(FormRequest $request): void
    {
        $clinic_id = $request->safe()->only('clinic_id')['clinic_id'] ?? ClinicCache::getCurrentClinic()->id ?? null;

        $doctor = $request->safe()->only('doctor_id')['doctor_id'] ?? null;
        if ($doctor) {
            $this->validateDoctorAndClinic($clinic_id, $doctor);
        }

        $this->validateDoctorAvailability(
            $request->safe()->only('schedule_id')['schedule_id'] ?? null,
            $request->safe()->only('doctor_id')['doctor_id'] ?? null,
            $request->safe()->only('from')['from'] ?? null,
            $request->safe()->only('to')['to'] ?? null
        );

        $this->repository->create([
            ...$request->validated(),
            'clinic_id' => $clinic_id ?? null,
        ]);
    }

    /**
     * Update a booking.
     */
    public function update(FormRequest $request, Booking $booking): void
    {
        $clinic_id = $request->safe()->only('clinic_id')['clinic_id'] ?? ClinicCache::getCurrentClinic()->id ?? null;

        $doctor = $request->safe()->only('doctor_id')['doctor_id'] ?? null;
        if ($doctor) {
            $this->validateDoctorAndClinic($clinic_id, $doctor);
        }

        $this->validateDoctorAvailability(
            $request->safe()->only('schedule_id')['schedule_id'] ?? null,
            $request->safe()->only('doctor_id')['doctor_id'] ?? null,
            $request->safe()->only('from')['from'] ?? null,
            $request->safe()->only('to')['to'] ?? null,
            $booking->id
        );

        $this->repository->update([
            ...$request->validated(),
            'clinic_id' => $clinic_id ?? null,
        ], $booking->id);
    }

    /**
     * Delete a booking.
     */
    public function delete(Booking $booking): void
    {
        $this->repository->delete($booking->id);
    }

    /**
     * Validate if the doctor is in the current clinic and has a doctor role.
     */
    private function validateDoctorAndClinic($clinic_id, $doctor): void
    {
        $clinicRepository = app(ClinicRepositoryInterface::class);
        $userHasClinic = $clinicRepository->isUserInClinic($clinic_id ?? null, $doctor);

        if (!$userHasClinic) {
            abort(403, 'You are not allowed to create booking for this doctor');
        }

        $userDoctor = app(UserRepository::class)->show($doctor);
        if (!$userDoctor->hasRole('doctor')) {
            abort(403, 'The doctor you selected does not have a doctor role');
        }
    }

    /**
     * Validate if the doctor is available in the selected schedule.
     */
    private function validateDoctorAvailability($scheduleId, $doctorId, $from, $to, $exceptId = null): void
    {
        if ($this->isDoctorAvailableInSchedule($scheduleId, $doctorId, $from, $to, $exceptId)) {
            abort(403, 'Doctor is not available in this schedule');
        }
    }

    /**
     * Determine if the doctor is available in the selected schedule.
     */
    private function isDoctorAvailableInSchedule(string $scheduleId, string $doctorId, string $from, string $to, string $exceptId = null): bool
    {
        return Booking::where('doctor_id', $doctorId)
            ->where('schedule_id', $scheduleId)
            ->where('from', $from)
            ->where('to', $to)
            ->when($exceptId, function ($q) use ($exceptId) {
                $q->where('id', '!=', $exceptId);
            })
            ->exists();
    }
}
