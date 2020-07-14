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

    /**
     * Open page with all files
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $files = $this->all();
        foreach ($files as $file)
            $file['size'] = round(Storage::size(Auth::user()->email."/".$file->path."/".$file->name) / (1024 * 1024), 4); //file size in mb

        return view('manager.file.index', compact('files'));
    }

    /**
     * Open uploading page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function upload()
    {
        $directories = Directory::where('user_id', Auth::user()->id)->get()->pluck('name', 'id');
        return view('manager.file.upload', compact('directories'));
    }

    /**
     * File creation
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'filenames' => 'required|max:10240',
            'directory' => 'required'
        ]);

        $directory = Directory::find($request->directory);

        if($request->hasfile('filenames') && $directory->user_id === $request->user()->id)
        {
            try{
                foreach($request->file('filenames') as $uploadFile)
                {
                    if( Storage::putFileAs($request->user()->email."/".$directory->path, $uploadFile, $uploadFile->getClientOriginalName())) {
                        $file = new File();
                        $file->name = $uploadFile->getClientOriginalName();
                        $file->folder_id = $request->directory;
                        $file->user_id = $request->user()->id;
                        $file->path = $directory->path;
                        $file->save();
                        Session::flash('status', 'Files was uploaded!');
                    }
                    else
                        return redirect()->back()->withErrors('Error with the save files!');
                }
            } catch (\Exception $e){
                logger()->error("Error in foreach",["store files" => $e->getMessage()]);
            }
        }
        else
            return redirect()->back()->withErrors('Error with the put files!');

        return redirect()->back();
    }

    /**
     * Delete file
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $file = File::find($request->id);

        if($this->checkAuthUserPermission($file->user_id, $file->permission)) {
            if(Storage::delete($request->user()->email."/".$file->path."/".$file->name)){
                File::destroy($request->id);
                return redirect()->back()->with('status', 'File has been deleted!');
            }
            else
                return redirect()->back()->withErrors('Error with delete file!');
        }
        else
            return redirect()->back()->withErrors('You do not have permissions!');
    }

    /**
     * Download file
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(Request $request)
    {
        $file = File::findOrFail($request->id);

        $response = $this->checkAuthUserPermission($file->user_id) ?
            response()->download(storage_path('app/'). $request->user()->email. "/".$file->path."/".$file->name) :
                redirect()->back()->withErrors('You do not have permissions!');

        return $response;
    }

    /**
     * Show images and also
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Request $request)
    {
        $file = File::findOrFail($request->id);

        if (in_array(FileSupport::extension($file->name),["png", "svg", "webp", "jpg", "jpeg", "ico", "pdf"]) && $this->checkAuthUserPermission($file->user_id))
            return response()->file(storage_path('app/'). $request->user()->email."/". $file->path."/".$file->name);
        else
            return redirect()->back()->withErrors('You can not see the file!');
    }

    /**
     * Moving file
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function rename(Request $request)
    {
        $this->validate($request, [
            'newName' => 'required|unique:App\Directory,name'
        ]);

        if(substr_count($request->newName, "/") > 0)
            return redirect()->back()->withErrors('New name must not contain character "/"!');

        $file = File::findOrFail($request->id);
        $path = $request->user()->email."/". $file->path."/";

        if(Storage::move($path.$file->name, $path.$request->newName)){
            $file->name = $request->newName; 
            $file->save();
            return redirect()->back()->with('status', 'File has been renamed!');
        }
        else
            return redirect()->back()->withErrors('Error with rename file!');
    }

    /**
     * Create public link
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function publicLink(Request $request)
    {
        try {
            $file = File::findOrFail($request->id);
            $path = $request->user()->email . "/" . $file->path . "/" . $file->name;
            $fileName = $request->user()->email . "_" . $file->name;

            if (Storage::copy($path, "public/" . $fileName)) {
                $file->public_link = asset("storage/" . $fileName);
                $file->save();
                return redirect()->back()->with('status', 'Public link is created!');
            } else
                return redirect()->back()->withErrors('Error with created link!');
        }
        catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
