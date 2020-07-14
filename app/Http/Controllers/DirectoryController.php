<?php

namespace App\Http\Controllers;

use App\Directory;
use App\Traits\CheckPermission;
use App\Traits\FileOperations;
use App\Traits\FolderOperations;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Validator;
use Auth;

class DirectoryController extends Controller
{
    use CheckPermission, FolderOperations;

    /**
     * Open page with all directories
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $directories = $this->all();
        return view('manager.directory.index', compact('directories'));
    }

    /**
     * Open creating page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $directories = $this->all()->prepend(["name" => 'Please Select', "id" => 0], null)->pluck('name', 'id');
        return view('manager.directory.create', compact('directories'));
    }

    /**
     * Directory creation
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'directoryName' => 'required|min:1|max:100|unique:App\Directory,name',
            'directory' => 'required'
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());
        else {
            if(!empty($request->directory)) {
                $dir = Directory::find($request->directory);
                $path = $dir->path."/".$request->directoryName;
                $request->directoryName = $dir->name . '/' . $request->directoryName;
            }
            else
                $path = $request->directoryName;

            if (Storage::disk('local')->exists($request->directoryName))
                return redirect()->back()->withErrors('Directory already exists!');

            try{
                if (Storage::makeDirectory(Auth::user()->email."/".$request->directoryName)) {
                    $directory = new Directory();
                    $directory->user_id = Auth::user()->id;
                    $directory->name = $request->directoryName;
                    $directory->path = $path;
                    $directory->save();

                    return redirect()->back()->with('status', 'Directory has been created!');
                }
                else
                    return redirect()->back()->withErrors('Error with save directory!');
            } catch (\Exception $e){
                logger()->error('Error make directory', ["make dir" => $e->getMessage()]);
            }
        }
    }

    /**
     * Delete directory and file attachments
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request)
    {
        $directory = Directory::find($request->id);

        if($this->checkAuthUserPermission($request->user()->id)) {
            if(Storage::deleteDirectory(Auth::user()->email."/".$directory->name)){
                Directory::destroy($request->id);
                return redirect()->back()->with('status', 'Directory has been deleted!');
            }
            else
                return redirect()->back()->withErrors('Error with delete directory!');
        }
        else
            return redirect()->back()->withErrors('You do not have permissions!');
    }
}
