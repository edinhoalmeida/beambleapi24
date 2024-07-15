<?php

namespace App\Observers;

use App\Models\Image;

use Illuminate\Support\Facades\Storage;

class ImageObserver
{

    // retrieved, creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored, and replicating.

    /**
     * Handle the image "updated" event.
     *
     * @param  \App\Models\Image  $image
     * @return void
     */
    public function updating(Image $image)
    {
        // desabilitado  - estava apagendo a nova imagem e não a antiga
        // Storage::disk('public')->delete($image->url);
        // Storage::disk('public')->delete($image->thumbnail);
    }

}
