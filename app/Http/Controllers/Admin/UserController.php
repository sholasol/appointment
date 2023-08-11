<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
        {
            $users = User::latest()->get();
            return $users;
        }

    public function store(){
        
        request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
        ]);
        
       return $user = User::create([
            'name' => request('name'),
            'email' => request('email'),
            'password' => bcrypt(request('password')),
        ]);
    }

    public function update(User $user){
         request()->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'sometimes|min:8',
        ]);

        $user->update([
            'name' => request('name'),
            'email' => request('email'),
            'password' => request('password') ? bcrypt(request('password')) : $user->password,
        ]);

        return $user;
    }

    // public function destroy(User $user){
    //     $user->delete();

    //     return response()->noContent();
    // }

    public function destroy(User $user){
    try {
        $user->delete();
        return response()->noContent();
    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while deleting the user.'], 500);
    }
}


}