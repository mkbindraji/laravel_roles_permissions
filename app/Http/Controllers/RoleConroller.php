<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleConroller extends Controller
{
    //This will will show roles page
    public function index(){
        $roles = Role::orderBy('name','ASC')->paginate(5);
        return view('roles.rolesList',[
            'roles' => $roles
        ]);
    }

    //This will will create roles page
    public function create(){
        $permissions = Permission::orderBy('name' , 'ASC')->get();
        return view('roles.createRole',[
            'permissions' => $permissions
        ]);
    }

    //This will will Insert a roles in DB
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|min:3',
            'permission' => 'array' // Ensure permission is an array
        ]);

        // If validation passes
        if ($validator->passes()) {
            // Create a new role
            $role = Role::create(['name' => $request->name]);

            // If permissions are provided, assign them to the role
            if (!empty($request->permission)) {
                foreach ($request->permission as $name) {
                    $role->givePermissionTo($name);
                }
            }

            return redirect()->route('roles.index')->with('success','Role added successfully.');
        }else{
            return redirect()->route('roles.create')->withInput()->withErrors($validator);
        }
    }
}
