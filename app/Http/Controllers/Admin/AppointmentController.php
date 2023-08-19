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
        ], [
            'title.required' => "Appointment title is required",
            'client.required' => "Client name is required",
            'start.required' => "Appointment time is required",
            'end.required' => "Appointment end time is required",
            'description.required' => "Please enter a description",
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


    public function edit(Appointment $appointment){
     
        return $appointment;
    }

    public function update(Appointment $appointment){
        $validated = request()->validate([
            'title' => 'required',
            'start' => 'required',
            'end' => 'required',
            'client' => 'required',
            'description' => 'required',
        ], [
            'title.required' => "Appointment title is required",
            'client.required' => "Client name is required",
            'start.required' => "Appointment time is required",
            'end.required' => "Appointment end time is required",
            'description.required' => "Please enter a description",
        ]);

        $appointment->update($validated);

        return response()->json(['success' => true]);
    }


    public function destroy(Appointment $appointment)
    {
        try {
            $appointment->delete();
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the appointment.'], 500);
        }
    }
 
   
}