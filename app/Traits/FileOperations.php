<?php

namespace App\Traits;

use Auth;
use App\Directory;
use App\File;
use Illuminate\Support\Facades\Storage;

trait FileOperations
{
    use FolderOperations;

    private function all()
    {
        $files = File::where('user_id', Auth::user()->id)->paginate(10); 
        $this->checkFiles($files);
        $this->checkDirectories(Directory::where('user_id', Auth::user()->id));
        return $files;
    }

    protected function checkFiles($files)
    {
        foreach ($files as $key => $file){
            $exists = Storage::disk('local')->exists(Auth::user()->email."/".$file->path);
            if(!$exists) {
                File::destroy($file->id);
                unset($files[$key]);
            }
        }
        return $files;
    }
}