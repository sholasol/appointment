<?php

namespace App\Http\Controllers\Admin;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppointmentController extends Controller
{
     public function index()
    {
        $query = Appointment::query()
            ->with('client:id,firstname,lastname')
            ->latest();

        if (request()->has('status')) {
            $query->where('status', request('status'));
        }

        return $query->get()
            ->map(fn($appointment) => [
                'id' => $appointment->id,
                'start_time' => $appointment->start_time->format('Y-m-d h:i A'),
                'end_time' => $appointment->end_time->format('Y-m-d h:i A'),
                'status' => $appointment->status,
                'client' => $appointment->client,
                'title' => $appointment->title,
            ]);
    }

    public function store(Request $request){
        $validated = request()->validate([
            'title' => 'required',
            'start' => 'required',
            'end' => 'required',
            'client' => 'required',
            'description' => 'required',
        ]);

        Appointment::create([
            'title' => $validated['title'],
            'client_id' => $validated['client'],
            'start_time' => $validated['start'],
            'end_time' => $validated['end'],
            'description' => $validated['description'],
            'status' => 1,
        ]);

        return response()->json(['message' => 'Appointment created successfully']);
    }

   
}