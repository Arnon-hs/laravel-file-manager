<?php

namespace App\Traits;

use Auth;
use App\Directory;
use Illuminate\Support\Facades\Storage;

trait FolderOperations
{
    /**
     * @return mixed
     */
    private function all()
    {
        $directories = Directory::where('user_id', Auth::user()->id)->paginate(10); //not empty todo
        $this->checkDirectories($directories);
        return $directories;
    }

    /**
     * @param $directories
     * @return mixed
     */
    protected function checkDirectories($directories)
    {
        foreach ($directories as $key => $dir){
            $exists = Storage::disk('local')->exists(Auth::user()->email."/".$dir->path);
            if(!$exists) {
                Directory::destroy($dir->id);
                unset($directories[$key]);
            }
        }
        return $directories;
    }
}