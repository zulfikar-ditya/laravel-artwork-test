<?php

namespace App\Http\Controllers\Response;

use App\Http\Controllers\Controller;
use App\Supports\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ResponseController extends Controller
{
    /**
     * Handle response file.
     */
    public function handleResponseFile(string $path): BinaryFileResponse
    {
        $path = Str::replaceArray('$', '/', $path);

        return $this->responseFile($path);
    }
}
