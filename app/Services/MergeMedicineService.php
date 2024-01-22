<?php

namespace App\Services;

use App\Enums\MedicineBatchTypeEnum;
use App\Interfaces\Repositories\MedicineBatchDetailRepositoryInterface;
use App\Interfaces\Repositories\MedicineBatchRepositoryInterface;
use App\Interfaces\Repositories\MergeMedicineRepositoryInterface;
use App\Interfaces\Services\MergeMedicineServiceInterface;
use App\Models\MedicineBatch;
use App\Services\Base\BaseService;
use App\Models\MergeMedicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MergeMedicineService extends BaseService implements MergeMedicineServiceInterface
{
    /**
     * The constructor service
     */
    public function __construct(private MergeMedicineRepositoryInterface $repository)
    {
        //
    }

    /**
     * datatable service
     */
    public function datatable(Request $request): LengthAwarePaginator
    {
        $medicineBatchRepository = app()->make(MedicineBatchRepositoryInterface::class);
        return $medicineBatchRepository->datatableIsMerge($request);
    }

    /**
     * Create service
     */
    public function create(FormRequest $request): void
    {
        $clinicService = app()->make(ClinicService::class);
        $clinic = $clinicService->getClinicId($request);

        // * validate the stock left
        // get the medicine ids from stock out
        $outMedicines = [];
        foreach ($request->medicine_id as $medicineKey => $medicineIds) {
            foreach ($medicineIds['medicine_id_out'] as $medicineParentIdKey => $medicineId) {
                $outMedicines[] = $medicineId;
            }
        }

        $medicineBatchDetailRepository = app()->make(MedicineBatchDetailRepositoryInterface::class);
        $stockLeft = $medicineBatchDetailRepository->getStockLeftMedicines($outMedicines ?? []);

        // ! loop through the stock left and check if the stock left is greater than
        if (in_array(0, array_column($stockLeft, 'total'))) {
            throw new \Exception('Stock left is not enough');
        }

        // create stock in for medicine
        $medicineBatch = new MedicineBatch();
        $medicineBatch->fill([
            'clinic_id' => $clinic,
            'date' => $request->date,
            'type' => MedicineBatchTypeEnum::IN,
            'is_merge' => true,
        ]);

        try {
            $medicineBatch->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        // loop through medicine_ids from request
        foreach ($request->medicine_id as $medicineKey => $medicineIds) {

            // create stock out for each medicine
            $outMedicineBatch = new MedicineBatch();
            $outMedicineBatch->fill([
                'clinic_id' => $clinic,
                'date' => $request->date,
                'type' => MedicineBatchTypeEnum::OUT,
            ]);

            try {
                $outMedicineBatch->save();
            } catch (\Throwable $th) {
                throw $th;
            }

            // create stock out details for each medicine
            $outMedicineBatchDetails = [];
            foreach ($medicineIds['medicine_id_out'] as $medicineIdKey => $medicineId) {
                $outMedicineBatchDetails[] = [
                    'medicine_id' => $medicineId,
                    'qty' => $medicineIds['medicine_qty'][$medicineIdKey],
                    'price' => 0,
                ];
            }

            try {
                $outMedicineBatch->medicineBatchDetails()->createMany($outMedicineBatchDetails);
            } catch (\Throwable $th) {
                throw $th;
            }

            // create stock in details
            $InMedicineBatchDetail = $medicineBatch->medicineBatchDetails()->create([
                'production_date' => $request->date,
                'expired_date' => $medicineIds['expired_date'],
                'medicine_id' => $medicineIds['medicine_id'],
                'qty' => $medicineIds['qty'],
                'price' => 0,
            ]);

            // create many merge stock
            foreach ($medicineIds['medicine_id_out'] as $medicineIdKey => $medicineId) {
                $mergeMedicine = new MergeMedicine();
                $mergeMedicine->fill([
                    'medicine_batch_id' => $outMedicineBatch->id,
                    'medicine_batch_detail_id' => $InMedicineBatchDetail->id,
                    'medicine_from_id' => $medicineId,
                ]);

                try {
                    $mergeMedicine->save();
                } catch (\Throwable $th) {
                    throw $th;
                }
            }
        }
    }

    // /**
    //  * Update service
    //  */
    // public function update(FormRequest $request, MergeMedicine $MergeMedicine): void
    // {
    //     $this->repository->update($request->validated(), $MergeMedicine->id);
    // }

    // /**
    //  * Delete service
    //  */
    // public function delete(MergeMedicine $MergeMedicine): void
    // {
    //     $this->repository->delete($MergeMedicine->id);
    // }
}
