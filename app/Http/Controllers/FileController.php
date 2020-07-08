<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Directory;
use App\File;
use App\Traits\FileOperations;
use Illuminate\Support\Facades\File as FileSupport;
use App\Traits\CheckPermission;

class FileController extends Controller
{
    use FileOperations, CheckPermission;

    public function index()
    {
        $files = $this->all();
        return view('manager.file.index', compact('files'));
    }

    public function upload()
    {
        $directories = Directory::where('user_id', Auth::user()->id)->get()->pluck('name', 'id');
        return view('manager.file.upload', compact('directories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'filenames' => 'required'
        ]);

        $directory = Directory::find($request->directory);

        if($request->hasfile('filenames') && $directory->user_id === $request->user()->id)
        {
            foreach($request->file('filenames') as $uploadFile)
            {
                if($path = Storage::putFileAs($directory->name, $uploadFile, $uploadFile->getClientOriginalName())) {
                    $file = new File();
                    $file->name = $uploadFile->getClientOriginalName();
                    $file->folder_id = $request->directory;
                    $file->user_id = $request->user()->id;
                    $file->path = $path;
                    $file->permission = 0;
                    $file->save();
                    Session::flash('status', 'Files was uploaded!');
                }
                else
                    return redirect()->back()->withErrors('Error with the save files!');
            }
        } else
            return redirect()->back()->withErrors('Error with the put files!');

        return redirect()->back();
    }

    public function delete(Request $request)
    {
        $file = File::find($request->id);

        if($this->checkAuthUserPermission($file->user_id, $file->permission)) { //todo subdir
            if(Storage::delete($file->path)){
                File::destroy($request->id);
                Session::flash('status', 'File has been deleted!');
            } else {
                return redirect()->back()->withErrors('Error with delete file!');
            }
        } else {
            return redirect()->back()->withErrors('You do not have permissions!');
        }
        return redirect()->back();
    }

    public function download(Request $request)
    {
        $file = File::findOrFail($request->id);
        $response = $this->checkAuthUserPermission($file->user_id, $file->permission) ?
            response()->download(storage_path('app/').$file->path) :
                redirect()->back()->withErrors('You do not have permissions!');
        return $response;
    }

    public function show(Request $request)
    {
        $file = File::findOrFail($request->id);
        if (in_array(FileSupport::extension($file->name),["png", "svg", "webp", "jpg", "jpeg", "ico", "pdf"]))
            return response()->file(storage_path('app/').$file->path);

         return redirect()->back()->withErrors('You can not see the file!');
    }

    public function rename(Request $request)
    {
        //todo rename
        $file = File::findOrFail($request->id);
        preg_match("~(.+/)~ui",$file->path,$path);
//        dd($path[1]);
        Storage::move($file->path, $path[1].'test.png');
    }
}
