<?php

use Illuminate\Support\Facades\Storage;

/**
 * Upload file to storage
 *
 * @param Illuminate\Http\Request $request 
 * 
 * @return string
 */
function uploadFile($request)
{
    return $request->file('image')
        ->storeAs(
            'image/products', time() . '-' . $request->file('image')
                ->getClientOriginalName(), 'public'
        );
}

/**
 * Remove image from storage
 * 
 * @param string $file_path 
 * 
 * @return boolean
 */
function removeFile($file_path)
{
    // unlink old imageDir
    return Storage::delete($file_path) ? true : false;
}
