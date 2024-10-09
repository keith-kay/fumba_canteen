<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Shifts;
use App\Models\CustomUser;
use App\Models\User_type;
use Illuminate\Support\Facades\Hash;

class HRController extends Controller
{
    public function createUser(){
        $roles = Role::pluck('name', 'name')->all();
        $role_list = array_diff($roles,['admin','super-admin','hr']);
        $shifts = Shifts::pluck('bsl_cmn_shifts_name', 'bsl_cmn_shifts_id')->all();
        $userTypes = User_type::pluck('bsl_cmn_user_types_name', 'bsl_cmn_user_types_id');
        return view('admin.hr.user.create', [
            'roles' => $role_list,
            'userTypes' => $userTypes,
            'shifts' => $shifts
        ]);
    }

    public function storeUser(Request $request){
        $pin = mt_rand(1000, 9999);
        echo $pin;
        try {
            $validatedData = $request->validate([
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'password' =>  'required|string|min:8',
                'employment_number' => 'nullable|string',
                'user_type_id' => 'required|string',
                'status' => 'required|string',
                'email' => 'nullable|email',
                'department' =>  'nullable|string',
                'roles' => 'required',
                'shift' => 'required',
            ]);

           
         /*   // Check if email exists before sending
            if (isset($validatedData['email'])) {
                Mail::to($validatedData['email'])->send(new PinMail($pin));
            }
                */
        } catch (\Exception $e) {
            // Return response with validation errors
            return response()->json(['errors' => $e->getMessage()], 422);
        }
        
        // Check if employment number already exists
        if ($validatedData['employment_number'] !== null && CustomUser::where('bsl_cmn_users_employment_number', $validatedData['employment_number'])->exists()) {
            return redirect()->back()->with('error', 'Employment number already exists.');
        }

        // Check if email already exists
        if ($validatedData['email'] && CustomUser::where('email', $validatedData['email'])->exists()) {
            return redirect()->back()->with('error', 'Email already exists.');
        }

        $user = CustomUser::create([
            'bsl_cmn_users_firstname' => $request->firstname,
            'bsl_cmn_users_lastname' => $request->lastname,
            'bsl_cmn_users_employment_number' => $request->employment_number,
            'password' => Hash::make($request->password),
            'bsl_cmn_users_type' => $request->user_type_id,
            'bsl_cmn_users_pin' => $pin,
            'bsl_cmn_users_status' => $request->status,
            'email' => $request->email,
            'bsl_cmn_users_department' => $request->department,
        ]);

        $user->syncRoles($request->roles);
        $user->shifts()->attach($request->input('shift'));

        return redirect('/admin/hr')->with('success', 'User created successfully with roles');
    }
}
