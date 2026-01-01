<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait ImageUpload
{
    public function UserImageUpload($image, $user)
    {

        // Get just Extension
        $extension = $image->getClientOriginalExtension();

        // Filename To store
        $file_name_to_store = $user. '_'. time().'.'.$extension;
        // Return file name
        return  $file_name_to_store ;
    }
}

?>
