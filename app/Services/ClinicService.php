<?php

namespace App\Services;

use App\Interfaces\Repositories\ClinicRepositoryInterface;
use App\Interfaces\Services\ClinicServiceInterface;
use App\Services\Base\BaseService;
use App\Models\Clinic;
use App\Support\ClinicCache;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ClinicService extends BaseService implements ClinicServiceInterface
{
    use UploadFile;

    /**
     * The constructor service
     */
    public function __construct(private ClinicRepositoryInterface $repository)
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
        $data = $this->repository->create([
            ...$request->safe()->except('image', 'clinic_polies', 'attachments'),
            'image' => $request->hasFile('image') ? $this->uploadFile($request->file('image'), 'clinic') : null
        ]);

        // * sync clinic polies
        $data->clinicPolies()->attach($request->clinic_polies);

        // * store clinic attachments
        $this->storeClinicAttachments($request, $data);
    }

    /**
     * show Service
     */
    public function show(Clinic $Clinic): Clinic
    {
        return $this->repository->show($Clinic->id);
    }

    /**
     * Update service
     */
    public function update(FormRequest $request, Clinic $Clinic): void
    {
        $oldFile = $Clinic->image;

        // * check if has file
        if ($request->hasFile('image')) {

            // * upload new file
            $image = $this->uploadFile($request->file('image'), 'clinic');
        }

        $data = $this->repository->update([
            ...$request->safe()->except('image', 'attachments', 'file_name', 'clinic_polies', 'deleted_attachments'),
            'image' => $image ?? $oldFile
        ], $Clinic->id);

        // * sync clinic polies
        $data->clinicPolies()->sync($request->clinic_polies);

        // * delete old file
        if (isset($image)) {
            $this->deleteFile($oldFile);
        }

        // * store clinic attachments
        $this->storeClinicAttachments($request, $data);

        // * id the delete_attachments request is an array and the count is greater than 0
        // * delete clinic attachments
        if (is_array($request->delete_attachments) && count($request->delete_attachments) > 0) {
            $this->deleteClinicAttachment($this->repository->getAttachments($Clinic, $request->delete_attachments));
        }
    }

    /**
     * Delete service
     */
    public function delete(Clinic $Clinic): void
    {
        // delete file
        $this->deleteFile($Clinic->image);

        // delete clinic attachments files
        $this->deleteClinicAttachment($Clinic->clinicAttachments);

        // delete clinic attachments
        $Clinic->clinicAttachments()->delete();

        $this->repository->delete($Clinic->id);
    }

    /**
     * Store the clinic attachment files
     */
    protected function storeClinicAttachments(FormRequest $request, \App\Models\Clinic $clinic): array
    {
        // store uploaded files to an array
        $files = [];

        $requestFiles = $request->safe(['attachments']);

        // ! if the request has attachments and request attachments count is greater than 0
        if (is_array($requestFiles) && count($requestFiles) > 0) {

            // * loop through files
            foreach ($request->attachments as $key => $file) {

                // * upload file
                $uploadedFile = $this->uploadFile($file, 'clinic-attachment');

                // * push file to array
                array_push($files, [
                    'name' => $request->file_name[$key],
                    'path' => $uploadedFile
                ]);
            }

            // ! if the files count is greater than 0
            if (count($files) > 0) {

                // * create many clinic attachments
                try {
                    $clinic->clinicAttachments()->createMany($files);
                } catch (\Throwable $th) {

                    // delete the uploaded file
                    foreach ($files as $file) {
                        $this->deleteFile($file['path']);
                    }

                    throw $th;
                }
            }
        }

        return $files;
    }

    /**
     * Delete clinic attachment file from given ids
     *
     * @param array $ids
     * @return void
     */
    public function deleteClinicAttachment(Collection $dataImages): void
    {
        // if count dataImages is 0
        if ($dataImages->count() == 0) {
            return;
        }

        // * delete all attachments
        $dataImages->each(function ($attachment) {
            $attachment->delete();
        });

        // * delete files
        foreach ($dataImages as $attachment) {
            $this->deleteFile($attachment->path);
        }
    }

    /**
     * Get clinic ID
     */
    public function getClinicId(FormRequest $request): string|null
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
}
