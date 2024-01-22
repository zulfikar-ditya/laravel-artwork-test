<?php

namespace App\Interfaces\Services;

use App\Interfaces\Base\BaseServiceInterface;
use Illuminate\Foundation\Http\FormRequest;

interface ClinicInvitationServiceInterface extends BaseServiceInterface
{
    /**
     * Create service
     */
    public function create(FormRequest $request, string $roleName): void;

    /**
     * Accept invitation
     */
    public function acceptInvitation(FormRequest $request): void;
}
