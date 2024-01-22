<?php

namespace App\Services;

use App\Interfaces\Repositories\MedicalRecordRepositoryInterface;
use App\Interfaces\Services\MedicalRecordServiceInterface;
use App\Services\Base\BaseService;
use App\Models\MedicalRecord;
use App\Repositories\PatientRepository;
use App\Support\ClinicCache;
use App\Traits\UploadFile;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class MedicalRecordService extends BaseService implements MedicalRecordServiceInterface
{
    use UploadFile;

    /**
     * The constructor service
     */
    public function __construct(private MedicalRecordRepositoryInterface $repository)
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
        $data = $this->repository->create($this->getMedicalRecordData($request));

        $this->storeMedicalRecordMedicine($data, $request);
        $this->storeMedicalRecordAttachments($data, $request);

        return;
    }

    /**
     * show service
     */
    public function show(MedicalRecord $MedicalRecord): MedicalRecord
    {
        return $this->repository->show($MedicalRecord->id);
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, MedicalRecord $MedicalRecord): void
    {
        $this->repository->update($this->getMedicalRecordData($request), $MedicalRecord->id);
        // delete all medicine and create new
        $MedicalRecord->medicalRecordMedicines()->delete();
        $this->storeMedicalRecordMedicine($MedicalRecord, $request);

        // store all new medical records attachment.
        $this->storeMedicalRecordAttachments($MedicalRecord, $request);

        // delete old attachment
        if (is_array($request->deleted_attachment_id) && count($request->deleted_attachment_id) > 0) {

            // * delete attachment from database
            $attachments = $this->repository->getWillDeletedAttachments($MedicalRecord, $request->deleted_attachment_id);
            foreach ($attachments as $attachment) {
                $this->deleteFile($attachment);
            }

            // * delete attachment from database
            $this->repository->deleteWillDeletedAttachments($MedicalRecord, $request->deleted_attachment_id);
        }

        return;
    }

    /**
     * Delete service
     */
    public function delete(MedicalRecord $MedicalRecord): void
    {
        // * delete all attachment
        $this->repository->deleteWillDeletedAttachments($MedicalRecord, $MedicalRecord->medicalRecordAttachments()->get()->pluck('id')->toArray());

        // * delete all medicine
        $MedicalRecord->medicalRecordMedicines()->delete();

        $this->repository->delete($MedicalRecord->id);
    }


    /**
     * Get medical record data from request
     */
    private function getMedicalRecordData(FormRequest $request): array
    {
        $clinic = $request->clinic_id;
        if (Auth::check() && Auth::user()->hasRole('superadmin')) {
            $clinic = $request->clinic_id;
        }

        // if user logged in
        // and user cache current_clinic
        if (Auth::check() && $clinicCache = ClinicCache::getCurrentClinicWithOutThrow()) {
            $clinic = $clinicCache->id;
        }

        if ($clinic === null) {
            throw new \Exception('Clinic is required');
        }

        // find user patient record
        // if not found create new patient data
        $patientRepository = new PatientRepository();
        $patient = $patientRepository->findByRequest($request);

        if (is_null($patient)) {
            throw new \Exception('Patient data not found');
        }

        return [
            'clinic_id' => $clinic,
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id ?? Auth::id(),
            'poly_id' => $request->poly_id,
            'date' => $request->date ?? Carbon::now(),
            'diagnosis' => $request->diagnosis,
            'therapy' => $request->therapy,
            'handling' => $request->handling,
            'description' => $request->description,
            'status' => 'Undefined',
        ];
    }

    /**
     * Store medical record medicine
     */
    private function storeMedicalRecordMedicine(MedicalRecord $medicalRecord, FormRequest $request): void
    {
        $medicineData = [];
        foreach ($request->medicine_id as $key => $medicine_id) {
            $medicineData[] = [
                'medicine_id' => $request->medicine_id[$key],
                'dosage_form' => $request->dosage_form[$key],
                'quantity' => (float) $request->quantity[$key],
                'description' => $request->medicine_description[$key] ?? null,
            ];
        }

        $medicalRecord->medicalRecordMedicines()->createMany($medicineData);
    }

    /**
     * Store medical record attachments
     */
    private function storeMedicalRecordAttachments(MedicalRecord $medicalRecord, FormRequest $request): void
    {
        $dataAttachments = [];

        if (!is_array($request->attachments)) {
            return;
        }

        foreach ($request->attachments as $key => $attachment) {

            $uploadFile = $this->uploadFile($attachment, 'medical-record');

            $dataAttachments[] = [
                'name' => $request->file_name[$key] ?? $attachment->getClientOriginalName(),
                'path' => $uploadFile,
            ];
        }

        try {
            if (count($dataAttachments) > 0) {
                $medicalRecord->medicalRecordAttachments()->createMany($dataAttachments);
            }
        } catch (\Throwable $th) {

            // * delete uploaded files if fails
            foreach ($dataAttachments as $attachment) {
                $this->deleteFile($attachment['path']);
            }

            throw $th;
        }
    }
}
