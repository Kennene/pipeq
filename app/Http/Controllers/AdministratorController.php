<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Destination;
use App\Models\DestinationsSchedule;
use App\Models\User;
use App\Models\Role;
use App\Models\TicketView;

class AdministratorController extends Controller
{
    public function index(): View
    {
        $tickets_history = TicketView::getHistory();
        $destinations    = Destination::with('schedules')->get();
        $users           = User::with('roles')->get();
        $roles           = Role::all();
        
        return view('administrator.administrator')
               ->with(compact('destinations', 'users', 'roles', 'tickets_history'));
    }

    /**
     * Update schedule of the destinations
     */
    public function updateSchedule(Request $request)
    {
        // Loop through the posted schedules
        foreach ($request->input('schedules', []) as $schedule_id => $data) {
            // Find the schedule record
            $schedule = DestinationsSchedule::find($schedule_id);
            if ($schedule) {
                $schedule->is_closed = $data['is_closed'];
                $schedule->open_time = $data['open_time'];
                $schedule->close_time = $data['close_time'];
                $schedule->save();
            }
        }

        // redirect back to the page
        return redirect()
            ->back()
            ->with('success', 'Schedules updated successfully!');
    }
}
