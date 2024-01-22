<?php

namespace App\Repositories;

use App\Enums\MedicineBatchTypeEnum;
use App\Interfaces\Repositories\MedicineBatchDetailRepositoryInterface;
use App\Repositories\Base\BaseRepository;
use App\Models\MedicineBatchDetail;
use App\Support\ClinicCache;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MedicineBatchDetailRepository extends BaseRepository implements MedicineBatchDetailRepositoryInterface
{
    /**
     * Instantiate a new MedicineBatchDetailRepository instance.
     */
    public function __construct()
    {
        $this->setAllowableSearch([
            //
        ]);

        $this->setAllowableSort([
            //
        ]);
    }

    /**
     * datatable stock mutation query
     */
    private function datatableStockMutationQuery(Request $request): Builder
    {
        return DB::table('medicine_batch_details')
            ->join('medicine_batches', function ($q) use ($request) {
                $q->on('medicine_batches.id', '=', 'medicine_batch_details.medicine_batch_id')
                    ->when($request->search, function ($query) use ($request) {
                        $query->orWhere('medicine_batches.code', 'ilike', '%' . $request->search . '%');
                    });
            })
            ->join('medicines', function ($q) use ($request) {
                $q->on('medicines.id', '=', 'medicine_batch_details.medicine_id')
                    ->when($request->search, function ($query) use ($request) {
                        $query->orWhere('medicines.name', 'ilike', '%' . $request->search . '%');
                    });
            })
            ->when($request->search, function ($q) use ($request) {
                $q->orWhere('medicine_batch_details.production_date', 'ilike', '%' . $request->search . '%')
                    ->orWhere('medicine_batch_details.expired_date', 'ilike', '%' . $request->search . '%')
                    ->orWhere('medicine_batch_details.qty', 'ilike', '%' . $request->search . '%')
                    ->orWhere('medicine_batch_details.price', 'ilike', '%' . $request->search . '%');
            })
            ->where('medicine_batches.clinic_id', ClinicCache::getCurrentClinic()->id)
            ->when($request->filter && is_array($request->filter) && array_key_exists('type', $request->filter), fn ($q) => $q->where('medicine_batches.type', $request->filter['type']))
            ->when($request->filter && is_array($request->filter) && array_key_exists('medicine_id', $request->filter), fn ($q) => $q->where('medicines.id', $request->filter['medicine_id']))
            ->when($request->filter && is_array($request->filter) && array_key_exists('expired_date', $request->filter), fn ($q) => $q->where('medicine_batch_details.expired_date', '>=', $request->filter['expired_date']))
            ->when($request->filter && is_array($request->filter) && array_key_exists('production_date', $request->filter), fn ($q) => $q->where('medicine_batch_details.production_date', '<=', $request->filter['production_date']))
            ->when($request->filter && is_array($request->filter) && array_key_exists('from_date', $request->filter), fn ($q) => $q->whereDate('medicine_batches.date', '>=', $request->filter['from_date']))
            ->when($request->filter && is_array($request->filter) && array_key_exists('to_date', $request->filter), fn ($q) => $q->whereDate('medicine_batches.date', '<=', $request->filter['to_date']))
            ->select([
                'medicine_batches.id as parent_id',
                'medicine_batches.code as code',
                'medicine_batches.date as date',
                'medicine_batches.type as type',
                'medicines.name as medicine_name',
                'medicines.brand as medicine_brand',
                'medicine_batch_details.production_date',
                'medicine_batch_details.expired_date',
                'medicine_batch_details.qty',
                'medicine_batch_details.price',
            ]);
    }

    /**
     * Datatable stock mutations
     */
    public function datatableStockMutations(Request $request): LengthAwarePaginator
    {
        return $this->datatableStockMutationQuery($request)
            ->orderBy('medicine_batch_details.ordering', 'desc')
            ->paginate($request->get('per_page') ?? config('app.default_paginator'));
    }

    /**
     * Datatable stock summary
     */
    public function datatableStockSummary(Request $request): LengthAwarePaginator
    {
        $medicineRepository = new MedicineRepository();
        $dataTable = $medicineRepository->datatable($request);

        foreach ($dataTable->items() as $item) {
            $item->stock_left = $this->getStockLeftEachItem($item->id);
        }

        return $dataTable;
    }

    /**
     * Create repository
     */
    public function create(array $data): MedicineBatchDetail
    {
        $data =  MedicineBatchDetail::create($data);

        return $data;
    }

    /**
     * Update repository
     */
    public function update(array $data, string $id): MedicineBatchDetail
    {
        $model = MedicineBatchDetail::findOrFail($id);
        $model->update($data);

        return $model;
    }

    /**
     * Delete repository
     */
    public function delete(string $id): void
    {
        $data = MedicineBatchDetail::findOrFail($id);
        $data->delete();
    }

    /**
     * Get stock left each item.
     */
    public function getStockLeftEachItem(string $medicineId): int
    {
        $inSum = MedicineBatchDetail::where('medicine_id', $medicineId)->whereHas('medicineBatch', function ($q) {
            $q->where('type', MedicineBatchTypeEnum::IN);
        })->sum('qty');

        $outSum = MedicineBatchDetail::where('medicine_id', $medicineId)->whereHas('medicineBatch', function ($q) {
            $q->where('type', MedicineBatchTypeEnum::OUT);
        })->sum('qty');

        return $inSum - $outSum;
    }

    /**
     * Get stock left medicines
     */
    public function getStockLeftMedicines(array $medicineIds)
    {
        $inSum = MedicineBatchDetail::whereIn('medicine_id', $medicineIds)->whereHas('medicineBatch', function ($q) {
            $q->where('type', MedicineBatchTypeEnum::IN);
        })
            ->withoutGlobalScope('orderingDesc')
            ->groupBy('medicine_id')
            ->selectRaw('sum(qty) as total, medicine_id')
            ->get();

        $outSum = MedicineBatchDetail::whereIn('medicine_id', $medicineIds)->whereHas('medicineBatch', function ($q) {
            $q->where('type', MedicineBatchTypeEnum::OUT);
        })
            ->withoutGlobalScope('orderingDesc')
            ->groupBy('medicine_id')
            ->selectRaw('sum(qty) as total, medicine_id')
            ->get();

        $result = [];

        foreach ($medicineIds as $medicineId) {
            $in = $inSum->where('medicine_id', $medicineId)->first();
            $out = $outSum->where('medicine_id', $medicineId)->first();

            $result[$medicineId] = $in?->total - $out?->total;
        }

        return $result;
    }
}
