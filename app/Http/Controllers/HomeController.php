<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomUser;

class HomeController extends Controller
{
    public function index()
    {
        //dd(auth()->user());
        return view('home');
    }

    public function hr(){
        $roles = auth()->user()->roles; 
        $role_list = $roles->pluck('name')->toArray();
        $guests = CustomUser::where('bsl_cmn_users_type', 5)->get();
        $interns = CustomUser::where('bsl_cmn_users_type', 6)->get();
        //remove irrelevant roles
        
        if (in_array('hr', $role_list) || in_array('super-admin', $role_list)){
           $users = CustomUser::with('userType')
                ->where('bsl_cmn_users_department', 'Security')
                ->get();
           return view('admin.hr.index')->with([
                'users' => $users,
                'guests' => $guests,
                'interns' => $interns,
            ]);
        }else{
            ## Not authorized
         return redirect()->route('admin.dashboard');
        }
    }
}
