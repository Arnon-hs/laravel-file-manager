<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Auth;
use App\Directory;
use App\File;

class FileController extends Controller
{
    public function index()
    {
        $files = File::where('user_id', Auth::user()->id)->get(); //not empty todo
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

        $directory = Directory::findOrFail($request->directory);

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
        $file = File::findOrFail($request->id);

        if($file->user_id === Auth::user()->id) { //todo subdir
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
        return response()->download(storage_path('app/').$file->path);
    }

    public function show(Request $request)
    {
        $file = File::findOrFail($request->id);
        return response()->file(storage_path('app/').$file->path);
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
