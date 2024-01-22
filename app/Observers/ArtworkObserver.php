<?php

namespace App\Observers;

use App\Models\Artwork;
use Illuminate\Support\Facades\Auth;

class ArtworkObserver
{
    /**
     * Handle the Artwork "creating" event.
     */
    public function creating(Artwork $artwork): void
    {
        if (is_null($artwork->user_id) and Auth::check()) {
            $artwork->user_id = auth()->user()->id;
        }
    }

    /**
     * Handle the Artwork "created" event.
     */
    public function created(Artwork $artwork): void
    {
        //
    }

    /**
     * Handle the Artwork "updated" event.
     */
    public function updated(Artwork $artwork): void
    {
        //
    }

    /**
     * Handle the Artwork "deleted" event.
     */
    public function deleted(Artwork $artwork): void
    {
        //
    }

    /**
     * Handle the Artwork "restored" event.
     */
    public function restored(Artwork $artwork): void
    {
        //
    }

    /**
     * Handle the Artwork "force deleted" event.
     */
    public function forceDeleted(Artwork $artwork): void
    {
        //
    }
}
