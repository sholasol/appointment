<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardstatsController extends Controller
{
    public function appointments(Request $request){
       
        $status = request('status');

        if($status =='all'){
            $totalAppointmentsCount = Appointment::count();
        }else{
            $totalAppointmentsCount = Appointment::where('status', $status)->count();
        }
        
        return response()->json([
            'totalAppointmentsCount' => $totalAppointmentsCount,
        ]);

       
    }

    public function users(Request $request){

        $totalUsersCount = User::query()
        ->when(request('date_range') === 'today', function($query){
            $query->whereBetween('created_at', [now()->today(), now()]);
        })
         ->when(request('date_range') === '7_days', function($query){
            $query->whereBetween('created_at', [now()->subDays(7), now()]);
        })
         ->when(request('date_range') === '30_days', function($query){
            $query->whereBetween('created_at', [now()->subDays(30), now()]);
        })
         ->when(request('date_range') === '60_days', function($query){
            $query->whereBetween('created_at', [now()->subDays(60), now()]);
        })
         ->when(request('date_range') === '360_days', function($query){
            $query->whereBetween('created_at', [now()->subDays(360), now()]);
        })
         ->when(request('date_range') === 'month_to_date', function($query){
            $query->whereBetween('created_at', [now()->firstOfMonth(), now()]);
        })
         ->when(request('date_range') === 'year_to_date', function($query){
            $query->whereBetween('created_at', [now()->firstOfYear(), now()]);
        })
        ->count();


        return response()->json([
            'totalUsersCount' => $totalUsersCount,
        ]);
    }


    public function DashboardStats(Request $request) {
        $scheduledCount = Appointment::where('status', 1)->count();
        $confirmedCount = Appointment::where('status', 2)->count();
        $cancelledCount = Appointment::where('status', 3)->count();

        return response()->json([
            'scheduledCount' => $scheduledCount,
            'confirmedCount' => $confirmedCount,
            'cancelledCount' => $cancelledCount,
        ]);
    }

}