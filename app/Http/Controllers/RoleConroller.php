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

    public function edit($id){
        $role = Role::findOrFail($id);
        $hasPermissions = $role->permissions->pluck('name');
        $permissions = Permission::orderBy('name' , 'ASC')->get();

        return view('roles.editRole',[
            'permissions' => $permissions,
            'hasPermissions' => $hasPermissions,
            'role' => $role
        ]);
    }

    public function update($id, Request $request){
        $role = Role::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,'. $id .',id',
        ]);

        // If validation passes
        if ($validator->passes()) {
            // update a new role
            $role->name = $request->name;
            $role->save();
            // If permissions are provided, assign them to the role
            if (!empty($request->permission)) {
                $role->syncPermissions($request->permission);
            }else{
                $role->syncPermissions([]);
            }

            return redirect()->route('roles.index')->with('success','Role added successfully.');
        }else{
            return redirect()->route('roles.edit', $id)->withInput()->withErrors($validator);
        }
    }

    public function destroy(Request $request){
        $id = $request->id;
        $role = Role::find($id);

        if($role == null){
            session()->flash('error', 'Role not found');
            return response()->json([
                'status' => false
            ]);
        }

        $role->delete();

        session()->flash('success', 'Role deleted successfully.');
        return response()->json([
            'status' => true
        ]);
    }
}
