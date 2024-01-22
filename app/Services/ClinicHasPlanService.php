<?php

namespace App\Services;

use App\Enums\ClinicStatusEnum;
use App\Interfaces\Repositories\ClinicHasPlanRepositoryInterface;
use App\Interfaces\Services\ClinicHasPlanServiceInterface;
use App\Services\Base\BaseService;
use App\Models\ClinicHasPlan;
use App\Support\BillingCycle;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class ClinicHasPlanService extends BaseService implements ClinicHasPlanServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private ClinicHasPlanRepositoryInterface $repository)
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
        $data = $request->validated();

        // find plan data form request
        $planRepository = app()->make(\App\Interfaces\Repositories\PlanRepositoryInterface::class);
        $plan = $planRepository->show($data['plan_id']);

        // find clinic
        $clinicRepository = app()->make(\App\Interfaces\Repositories\ClinicRepositoryInterface::class);
        $clinic = $clinicRepository->show($data['clinic_id']);

        // find clinic plans
        $clinicHasPlanRepository = app()->make(\App\Interfaces\Repositories\ClinicHasPlanRepositoryInterface::class);
        $clinicHasPlan = $clinicHasPlanRepository->getActivePlan($data['clinic_id']);

        // check if the clinic has another plan
        // deactivate the plan
        if ($clinicHasPlan and $clinicHasPlan->active) {
            // deactivate the plan
            $clinicHasPlanRepository->update(['active' => false], $clinicHasPlan->id);
        }

        // create new clinic has plan
        // extend expired plan from current date to billing cycle
        $this->repository->create([
            'clinic_id' => $data['clinic_id'],
            'plan_id' => $data['plan_id'],
            'billing_cycle' => $plan->billing_cycle,
            'from' => Carbon::now(),
            'to' => (new BillingCycle())->addDaysAmount($plan->billing_cycle),
            'active' => true,
        ]);

        // if the clinic status is in inactive and unpaid
        // change the status to active.
        if (in_array($clinic->status, [ClinicStatusEnum::INACTIVE, ClinicStatusEnum::UNPAID])) {
            $clinicRepository->update(['active' => true], $clinic->id);
        }
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, ClinicHasPlan $ClinicHasPlan): void
    {
        $planRepository = app()->make(\App\Interfaces\Repositories\PlanRepositoryInterface::class);
        $plan = $planRepository->show($request->safe()->only('plan_id')['plan_id'] ?? null);

        $this->repository->update($request->validated(), $ClinicHasPlan->id);
    }

    /**
     * Delete service
     */
    public function delete(ClinicHasPlan $ClinicHasPlan): void
    {
        $this->repository->delete($ClinicHasPlan->id);
    }
}
