<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);

        $user = User::create($request->all());

        return redirect('/dashboard/user')->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = User::find($id);
        return view('pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        // Check if password is provided and not null
        if ($request->has('password') && $request->password !== null) {
            $password = bcrypt($request->password); // You may want to hash the password before storing it
        } else {
            $password = $user->password; // Keep the existing password
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $password, // Assign the password variable
        ]);
        return redirect('/dashboard/user')->with('success', 'User updated successfully');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('/dashboard/user')->with('success', 'User deleted successfully');
    }
}
