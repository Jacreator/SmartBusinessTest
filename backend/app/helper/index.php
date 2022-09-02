<?php

use Illuminate\Support\Facades\Storage;

function uploadFile($request)
{
    return $request->file('image')
        ->storeAs('image/products', time() . '-' . $request->file('image')
            ->getClientOriginalName(), 'public');
}

function removeFile($file_path)
{
    // unlink old imageDir
    return Storage::delete($file_path) ? true : false;
}
