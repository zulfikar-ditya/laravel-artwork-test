<?php

namespace App\Services;

use App\Interfaces\Repositories\MedicineBatchRepositoryInterface;
use App\Interfaces\Services\MedicineBatchServiceInterface;
use App\Services\Base\BaseService;
use App\Models\MedicineBatch;
use App\Support\ClinicCache;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class MedicineBatchService extends BaseService implements MedicineBatchServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private MedicineBatchRepositoryInterface $repository)
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
        $clinic = $this->getClinicId($request);

        $model = $this->repository->create([
            'clinic_id' => $clinic,
            'date' => $request->date,
            'type' => $request->type,
        ]);

        $this->createDetails($model, $request);

        return;
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, MedicineBatch $MedicineBatch): void
    {
        $clinic = $this->getClinicId($request);

        $this->repository->update([
            'clinic_id' => $clinic,
            'date' => $request->date,
            'type' => $request->type,
        ], $MedicineBatch->id);

        // delete all details and create new.
        $MedicineBatch->medicineBatchDetails()->delete();
        $this->createDetails($MedicineBatch, $request);

        return;
    }

    /**
     * Delete service
     */
    public function delete(MedicineBatch $MedicineBatch): void
    {
        // delete all details
        $MedicineBatch->medicineBatchDetails()->delete();
        $this->repository->delete($MedicineBatch->id);
    }

    /**
     * Get clinic ID
     */
    private function getClinicId(FormRequest $request): string|null
    {
        $clinic = $request->clinic_id;
        if (Auth::check() && Auth::user()->hasRole('superadmin')) {
            $clinic = $request->clinic_id;
        }

        // if user logged in and user cache current_clinic
        if (Auth::check() && $clinicCache = ClinicCache::getCurrentClinicWithOutThrow()) {
            $clinic = $clinicCache->id;
        }

        if ($clinic === null) {
            throw new \Exception('Clinic is required');
        }

        return $clinic;
    }

    /**
     * Create or update details
     */
    private function createDetails($model, FormRequest $request): void
    {
        $data = $request->safe()->only(['medicine_id', 'production_date', 'expired_date', 'qty', 'price']);
        $details = [];
        if (is_array($request->safe()->only(['medicine_id']))) {
            foreach ($data['medicine_id'] as $key => $value) {
                $details[] = [
                    'medicine_id' => $value,
                    'production_date' => $data['production_date'][$key],
                    'expired_date' => $data['expired_date'][$key],
                    'qty' => $data['qty'][$key],
                    'price' => $data['price'][$key],
                ];
            }
        }

        try {
            $model->medicineBatchDetails()->createMany($details);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
