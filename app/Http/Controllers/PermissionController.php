<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    //This method will show permission page
    public function index(){
        return view('permissions.list');
    }

    //This method will show created permission page
    public function create(){
        return view('permissions.create');
    }

    //This method will show insert a permission in DB
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required|unique:permissions|min:3'
        ]);

        if($validator->passes()){
            Permission::create(['name' => $request->name]);
            return redirect()->route('permissions.index')->with('success','Permission added successfully.');
        }else{
            return redirect()->route('permissions.create')->withInput()->withErrors($validator);
        }
    }

    //This method will show edit a permission page
    public function edit(){

    }

    //This method will show update a permission page
    public function update(){

    }

      //This method will show delete a permission in DB
      public function destroy(){

      }
}
