<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addUsers()
    {
        return view('users.index');
    }

    public function createUser(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'first_name'=>$request->input('first_name'),
            'last_name'=>$request->input('last_name'),
            'email'=>$request->input('email'),
            'password'=>bcrypt($request->get('password'))
        ]);
        return redirect()->route('adaugare-utilizatori');
    }

    public function viewPermission()
    {
        $users = User::orderBy('id', 'DESC')->get();
        return view('users.permissions', compact('users'));
    }

    public function postAdminAssignRoles(Request $request)
    {
        $user = User::where('email', $request['email'])->first();
        $user->roles()->detach();
        if ($request['role_admin']) {
            $user->roles()->attach(Role::where('name', 'Admin')->first());
        }
        if ($request['role_ingineri']) {
            $user->roles()->attach(Role::where('name', 'Ingineri')->first());
        }
        if ($request['role_hr']) {
            $user->roles()->attach(Role::where('name', 'HR')->first());
        }
        if ($request['role_magazie']) {
            $user->roles()->attach(Role::where('name', 'Magazie')->first());
        }
        return redirect()->back();
    }

    public function edit($id)
    {
        $user = User::find($id);
        return view('users.edit',compact('user'));
    }

    public function update(Request $request, $id)
    {

        User::find($id)->update($request->all());
        return redirect()->route('acordare-permisii')
            ->with('success','Utilizator actualizat!');
    }
}
