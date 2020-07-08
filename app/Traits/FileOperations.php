<?php

namespace App\Traits;

use Auth;
use App\Directory;
use App\File;
use Illuminate\Support\Facades\Storage;

trait FileOperations
{
    private function all()
    {
        $files = File::where('user_id', Auth::user()->id)->get(); //not empty todo
        foreach ($files as $key => $file){
            $exists = Storage::disk('local')->exists($file->path);
            if(!$exists) {
//                Storage::delete($file->path);
                File::destroy($file->id);
                unset($files[$key]);
            }
        }

        return $files;
    }
}