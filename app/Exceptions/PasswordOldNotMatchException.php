<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class PasswordOldNotMatchException extends Exception
{
    /**
     * Instantiate a new PasswordOldNotMatchException instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct('Old password not match');
    }

    /**
     * Render the password old not match exception into an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [
                'old_password' => [
                    $this->getMessage(),
                ],
            ],
        ], 422);
    }
}
