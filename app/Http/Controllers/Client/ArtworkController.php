<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\ArtworkServiceInterface;

class ArtworkController extends Controller
{
    /**
     * Display the listing of artwork.
     */
    public function index(ArtworkServiceInterface $artworkService)
    {
        return $this->responseJson($artworkService->datatable(request()));
    }
}
