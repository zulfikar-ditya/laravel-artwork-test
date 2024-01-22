<?php

namespace App\Services;

use App\Interfaces\Services\ClinicInvitationServiceInterface;
use App\Jobs\User\SendEmailInviteJoinToAClinic;
use App\Models\Clinic;
use App\Services\Base\BaseService;
use App\Models\ClinicInvitation;
use App\Models\Role;
use App\Models\User;
use App\Models\UserHasClinic;
use App\Repositories\ClinicRepository;
use App\Support\ClinicCache;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;

class ClinicInvitationService extends BaseService implements ClinicInvitationServiceInterface
{
    /**
     * Create service
     */
    public function create(FormRequest $request, $roleName): void
    {
        $clinic = Clinic::findOrFail(ClinicCache::getCurrentClinic()->id);
        $user = User::where('email', $request->email)->firstOrFail();
        $role = Role::where('name', $roleName)->firstOrFail();

        // * check if user already in clinic
        if ($user->clinics->contains($clinic->id)) {
            throw new \Exception('User already in clinic');
        }


        // * create clinic invitation
        $clinicInvitation = \App\Models\ClinicInvitation::create([
            'clinic_id' => $clinic->id,
            'user_id' => $user->id,
            'role_id' => $role->id,
            'message' => $request->message,
        ]);

        // * send email
        SendEmailInviteJoinToAClinic::dispatch($user, $clinic, $clinicInvitation, $request->message);
    }

    /**
     * Accept invitation
     */
    public function acceptInvitation(FormRequest $request): void
    {
        $token = $request->token;

        $clinicInvitation = ClinicInvitation::with(['role'])->where('token', $token)->firstOrFail();

        // check if expired or not
        if (Carbon::now()->gt($clinicInvitation->expired_at)) {
            throw new \Exception('Invitation expired');
        }

        // check if the token is already user
        if ($clinicInvitation->is_used) {
            throw new \Exception('User already in clinic');
        }

        // check if the logged in user is not same with clinic invitation,
        if ($clinicInvitation->user_id !== auth()->user()->id) {
            throw new \Exception('You are not allowed to accept this invitation');
        }

        $userHasClinic = UserHasClinic::where('user_id', $clinicInvitation->user_id)
            ->where('clinic_id', $clinicInvitation->clinic_id)
            ->first();
        if ($userHasClinic) {
            throw new \Exception('User already in clinic');
        }

        // update clinic invitation
        $clinicInvitation->update([
            'is_used' => true,
        ]);

        // update user clinic
        $user = User::findOrFail($clinicInvitation->user_id);
        $user->syncClinics([
            [
                'clinic_id' => $clinicInvitation->clinic_id,
                'role_id' => $clinicInvitation->role_id,
            ]
        ]);

        // sync user with role doctor
        $user->syncRoles([$clinicInvitation->role->name]);

        // update cache
        $clinicRepository = app(ClinicRepository::class)->getUserClinic($clinicInvitation->clinic_id);
        ClinicCache::setCurrentClinic($clinicRepository);
    }
}
