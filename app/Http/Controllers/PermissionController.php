<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{

    public static function middleware() : array
    {
        return [
            new Middleware('permission:view permission', only : ['index']),
            new Middleware('permission:edit permission', only : ['edit']),
            new Middleware('permission:create permission', only : ['create']),
            new Middleware('permission:delete permission', only : ['destroy']),
        ];
    }

    //This method will show permission page
    public function index(){
        $permissions = Permission::orderBy('created_at', 'DESC')->paginate(20);
        return view('permissions.list', [
            'permissions' => $permissions
        ]);
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
    public function edit($id){
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', [
            'permission' => $permission
        ]);
    }

    //This method will show update a permission page
    public function update($id, Request $request){
        $permission = Permission::findOrFail($id);

        $validator = Validator::make($request->all(),[
            'name' => 'required|min:3|unique:permissions,name, '. $id .' ,id'
        ]);

        if($validator->passes()){
            $permission->name = $request->name;
            $permission->save();

            return redirect()->route('permissions.index')->with('success','Permission Updeted successfully.');
        }else{
            return redirect()->route('permissions.edit', $id)->withInput()->withErrors($validator);
        }
    }

    //This method will show delete a permission in DB
    public function destroy(Request $request){
        $id = $request->id;

        $permission = Permission::find($id);

        if($permission == null){
            session()->flash('error', 'Permission nat found');
            return response()->json([
                'status' => false
            ]);
        }

        $permission->delete();

        session()->flash('success', 'Permission Deleted Successfully.');
        return response()->json([
            'status' => true
        ]);
    }
}
