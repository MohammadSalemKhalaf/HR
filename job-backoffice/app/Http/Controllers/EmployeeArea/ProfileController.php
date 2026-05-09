<?php

namespace App\Http\Controllers\EmployeeArea;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->with('department', 'company', 'manager')->first();

        return view('employee.profile.show', compact('employee'));
    }

    public function edit(Request $request)
    {
        $user = $request->user();
        $employee = Employee::where('user_id', $user->id)->with('department')->first();

        return view('employee.profile.edit', compact('employee'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return Redirect::route('employee.profile.show')->with('success', 'Profile updated.');
    }
}
