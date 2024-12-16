<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Logs;
use App\Services\PrintHelper;
use App\Models\Sites;
use App\Models\CustomUser;
use App\Models\User_type;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MealticketController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Fetch all records if search is provided
            $search = trim($request->input('search.value', ''));

            if (!empty($search)) {
                // Perform search on the whole data set
                $searchQuery = Logs::with('user.userType', 'mealType', 'site')->where(function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('bsl_cmn_users_firstname', 'like', "%$search%")
                            ->orWhere('bsl_cmn_users_lastname', 'like', "%$search%")
                            ->orWhere('bsl_cmn_users_employment_number', 'like', "%$search%");
                    })
                    ->orWhereHas('mealType', function ($q) use ($search) {
                        $q->where('bsl_cmn_mealtypes_mealname', 'like', "%$search%");
                    })
                    ->orWhereHas('site', function ($q) use ($search) {
                        $q->where('bsl_cmn_sites_name', 'like', "%$search%");
                    })
                    ->orWhere('bsl_cmn_logs_time', 'like', "%$search%");
                });

                // Apply pagination to search results
                $recordsTotal = Logs::count(); // Total records in the database
                $recordsFiltered = $searchQuery->count(); // Total records matching the search
                $logsData = $searchQuery
                    ->skip($request->input('start', 0))
                    ->take($request->input('length', 10))
                    ->get();
            } else {
                // Fetch only today's logs when no search term is provided
                $logsQuery = Logs::with('user.userType', 'mealType', 'site')
                    ->whereDate('bsl_cmn_logs_time', Carbon::today());

                $recordsTotal = Logs::count(); // Total records in the database
                $recordsFiltered = $logsQuery->count(); // Total records for today's logs
                $logsData = $logsQuery
                    ->skip($request->input('start', 0))
                    ->take($request->input('length', 10))
                    ->get();
            }

            // Prepare data for DataTables
            $data = $logsData->map(function ($log) {
                $shiftType = (Carbon::parse($log->bsl_cmn_logs_time)->between(Carbon::parse('07:00'), Carbon::parse('19:00')))
                    ? 'Day Shift' : 'Night Shift';

                return [
                    'full_name' => $log->user->bsl_cmn_users_firstname . ' ' . $log->user->bsl_cmn_users_lastname,
                    'employment_number' => $log->user->bsl_cmn_users_employment_number,
                    'user_type' => optional($log->user->userType)->bsl_cmn_user_types_name ?? 'N/A',
                    'site' => optional($log->site)->bsl_cmn_sites_name ?? 'N/A',
                    'department' => $log->user->bsl_cmn_users_department ?? 'N/A',
                    'meal_type' => optional($log->mealType)->bsl_cmn_mealtypes_mealname ?? 'N/A',
                    'bsl_cmn_logs_time' => $log->bsl_cmn_logs_time,
                    'bsl_cmn_logs_id' => $log->bsl_cmn_logs_id
                ];
            });

            // Return DataTables response
            return response()->json([
                'draw' => (int)$request->input('draw', 1),
                'recordsTotal' => $recordsTotal, // Total records in the database
                'recordsFiltered' => $recordsFiltered, // Filtered records based on search or today's data
                'data' => $data
            ]);
        }

        // For non-AJAX requests
        return view('admin.tickets');
    }

    public function fetchCompanies()
    {
        $companies = User_type::pluck('bsl_cmn_user_types_name');
        return response()->json($companies);
    }
    public function fetchSites()
    {
        $sites = Sites::select('bsl_cmn_sites_id', 'bsl_cmn_sites_name')->get();
        return response()->json($sites);
    }
    public function fetchDepartments()
    {
        $department = CustomUser::pluck('bsl_cmn_users_department');
        return response()->json($department);
    }

    public function printTicket($ticketid)
    {
        
        // Retrieve the log entry
       
        $log = Logs::with('user.userType', 'mealType', 'site')
              ->where('bsl_cmn_logs_id', $ticketid)
              ->first();
        
    
        // Prepare the data for printing
        try
        {
            $mealDetails = (object) [
                'userid' => $log->user->bsl_cmn_users_id,
                'userName' => $log->user->bsl_cmn_users_firstname . ' ' . $log->user->bsl_cmn_users_lastname,
                'staffid' => $log->user->bsl_cmn_users_employment_number,
                'department' => $log->user->bsl_cmn_users_department,
                'company' => $log->user->userType->bsl_cmn_user_types_name,
                'mealtype' => $log->mealType->bsl_cmn_mealtypes_mealname,
                'date' => $log->bsl_cmn_logs_time,
                'site' => $log->site->bsl_cmn_sites_name
            ];

            $siteName = $log->site->bsl_cmn_sites_name;
            $site = Sites::where('bsl_cmn_sites_name', $siteName)->first();
            $sitePrinter = $site->printer->first();

            $printer = new PrintHelper($sitePrinter->address, $sitePrinter->port);
            $printer->printMealTicket($mealDetails);

            return redirect()->back()->with('success', 'Print jobs sent successfully.');
        }
        catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error printing tickets: ' . $e->getMessage());

            // Return an error response with the exception message
            return redirect()->back()->with('error', 'Failed to send print job: ' . $e->getMessage());
    }
}
    public function destroy($ticketid)
    {
        
        $ticket = Logs::findOrFail($ticketid);
        $ticket->delete();

        return redirect('ticket')->with('error', 'Meal Ticket deleted!');
    }
    
}

