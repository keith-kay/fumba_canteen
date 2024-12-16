<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;
use App\Models\User_type;
use App\Models\Sites;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogsExport;


class ReportController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch logs for today
            $logsQuery = Logs::with('user.userType', 'mealType', 'site')
                ->whereDate('bsl_cmn_logs_time', Carbon::today()); // Filter today's logs

            // Total count without pagination
            $recordsTotal = (clone $logsQuery)->count();

            // Apply pagination
            $logsData = $logsQuery->skip($request->input('start', 0))
                ->take($request->input('length', 10))
                ->get();

            // Prepare data for DataTables
            $data = [];
            foreach ($logsData as $log) {
                $shiftType = (Carbon::parse($log->bsl_cmn_logs_time)->between(Carbon::parse('07:00'), Carbon::parse('19:00')))
                    ? 'Day Shift' : 'Night Shift';

                $data[] = [
                    'full_name' => $log->user->bsl_cmn_users_firstname . ' ' . $log->user->bsl_cmn_users_lastname,
                    'employment_number' => $log->user->bsl_cmn_users_employment_number,
                    'user_type' => optional($log->user->userType)->bsl_cmn_user_types_name ?? 'N/A',
                    'site' => optional($log->site)->bsl_cmn_sites_name ?? 'N/A',
                    'department' => $log->user->bsl_cmn_users_department ?? 'N/A',
                    'meal_type' => optional($log->mealType)->bsl_cmn_mealtypes_mealname ?? 'N/A',
                    'bsl_cmn_logs_time' => $log->bsl_cmn_logs_time,
                    'shift' => $shiftType
                ];
            }

            // Return the DataTables response as JSON
            return response()->json([
                'draw' => (int)$request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $data
            ]);
        }

        // Return the view if it's not an AJAX request
        return view('admin.reports');
    }

    public function filteredLogs(Request $request)
    {
        $query = Logs::with('user.userType', 'mealType', 'site');
        
        // Apply date filters
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereDate('bsl_cmn_logs.bsl_cmn_logs_time', '>=', $request->from_date)
                ->whereDate('bsl_cmn_logs.bsl_cmn_logs_time', '<=', $request->to_date);
        }

        // Apply meal type filters (uncomment if needed)
        // if ($request->filled('meal_types')) {
        //     $mealTypes = explode(',', $request->meal_types);
        //     $query->whereIn('bsl_cmn_logs.bsl_cmn_logs_mealtype', $mealTypes);
        // }

        // Apply company filter
        if ($request->filled('company')) {
            $query->join('bsl_cmn_users', 'bsl_cmn_logs.bsl_cmn_logs_person', '=', 'bsl_cmn_users.bsl_cmn_users_id')
                ->where('bsl_cmn_users.bsl_cmn_users_type', $request->company);
        }

        // Apply site filter
        if ($request->filled('site')) {
            $query->where('bsl_cmn_logs.bsl_cmn_logs_site', $request->site);
        }

        // Total count without pagination
        $recordsTotal = (clone $query)->count();

        // Apply pagination
        $logsData = $query->skip($request->input('start', 0))
                        ->take($request->input('length', 10))
                        ->get();

        // Prepare data for DataTables
        $data = [];
        foreach ($logsData as $log) {
            // Determine shift type based on time

            $time = Carbon::parse($log->bsl_cmn_logs_time);

            $shiftType = ($time->hour >= 7 && $time->hour < 19) ? 'Day Shift' : 'Night Shift';          

            $data[] = [
                'full_name' => $log->user->bsl_cmn_users_firstname . ' ' . $log->user->bsl_cmn_users_lastname,
                'employment_number' => $log->user->bsl_cmn_users_employment_number,
                'user_type' => optional($log->user->userType)->bsl_cmn_user_types_name ?? 'N/A',
                'site' => optional($log->site)->bsl_cmn_sites_name ?? 'N/A',
                'department' => $log->user->bsl_cmn_users_department ?? 'N/A',
                'meal_type' => optional($log->mealType)->bsl_cmn_mealtypes_mealname ?? 'N/A',
                'bsl_cmn_logs_time' => $log->bsl_cmn_logs_time,
                'shift' => $shiftType
            ];
        }

        // Return the DataTables response as JSON
        return response()->json([
            'draw' => (int)$request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal, // Or use another count if you want filtered results
            'data' => $data
        ]);
    }

    public function export(Request $request) 
    {
        
        // Filter data based on the request parameters
        $query = Logs::with('user.userType', 'mealType', 'site');
    
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereDate('bsl_cmn_logs.bsl_cmn_logs_time', '>=', $request->from_date)
                  ->whereDate('bsl_cmn_logs.bsl_cmn_logs_time', '<=', $request->to_date);
        }
    
        if ($request->filled('company')) {
            $query->join('bsl_cmn_users', 'bsl_cmn_logs.bsl_cmn_logs_person', '=', 'bsl_cmn_users.bsl_cmn_users_id')
                  ->where('bsl_cmn_users.bsl_cmn_users_type', $request->company);
        }
    
        if ($request->filled('site')) {
            $query->where('bsl_cmn_logs.bsl_cmn_logs_site', $request->site);
        }
    
        // Retrieve filtered data
        $logsData = $query->get();
    
        // Prepare data for the export
        $data = [];
        foreach ($logsData as $log) {

            $time = Carbon::parse($log->bsl_cmn_logs_time);

            $shiftType = ($time->hour >= 7 && $time->hour < 19) ? 'Day Shift' : 'Night Shift';           
    
            $data[] = [
                'Full Name' => $log->user->bsl_cmn_users_firstname . ' ' . $log->user->bsl_cmn_users_lastname,
                'Employment Number' => $log->user->bsl_cmn_users_employment_number ?? 'N/A',
                'User Type' => optional($log->user->userType)->bsl_cmn_user_types_name ?? 'N/A',
                'Site' => optional($log->site)->bsl_cmn_sites_name ?? 'N/A',
                'Department' => $log->user->bsl_cmn_users_department ?? 'N/A',
                'Meal Type' => optional($log->mealType)->bsl_cmn_mealtypes_mealname ?? 'N/A',
                'Log Time' => $log->bsl_cmn_logs_time,
                'Shift' => $shiftType
            ];
        }
    
        // Return the data as JSON
        
        return response()->json($data);
    }
    

   
    public function fetchCompanies()
    {
        $companies = User_type::select('bsl_cmn_user_types_id', 'bsl_cmn_user_types_name')->get();
        return response()->json($companies);
    }
    public function fetchSites()
    {
        $sites = Sites::select('bsl_cmn_sites_id','bsl_cmn_sites_name')->get();
        return response()->json($sites);
    }
}

