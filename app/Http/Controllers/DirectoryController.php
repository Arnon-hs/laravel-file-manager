<?php

namespace App\Http\Controllers;

use App\Directory;
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
    public function index()
    {
        $directories = Directory::where('user_id', Auth::user()->id)->get();//todo not empty
        return view('manager.directory.index', compact('directories'));
    }

    public function create()
    {
        return view('manager.directory.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'dirName' => 'required|min:1|max:100',
        ]);

        if ($validator->fails())
            return redirect()->back()->withErrors($validator->errors());
        else {
            if(Storage::makeDirectory($request->dirName)){

                $directory = new Directory();
                $directory->user_id = Auth::user()->id;
                $directory->name = $request->dirName;
                $directory->permission = 0;
                $directory->save();

                Session::flash('status', 'Directory has been created!');
            } else {
                return redirect()->back()->withErrors('Error with save directory!');
            }
            return redirect()->back();
        }
    }

    public function delete(Request $request)
    {
        $directory = Directory::find($request->id);

        if($directory->user_id === Auth::user()->id) { //todo subdir
            if(Storage::deleteDirectory($directory->name)){
                Directory::destroy($request->id);
                Session::flash('status', 'Directory has been deleted!');
            } else {
                return redirect()->back()->withErrors('Error with delete directory!');
            }
        } else {
            return redirect()->back()->withErrors('You do not have permissions!');
        }
        return redirect()->back();
    }
}
