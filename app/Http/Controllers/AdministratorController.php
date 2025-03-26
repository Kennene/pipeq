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

        $translations = [];

        foreach (config('app.available_locales') as $locale => $language) {
            $path = base_path("lang/{$locale}.json");

            if (!file_exists($path)) {
                continue;
            }

            $json = json_decode(file_get_contents($path), true);

            if (!is_array($json)) {
                continue;
            }

            foreach ($json as $key => $value) {
                $translations[$key][$locale] = $value;
            }
        }
        
        return view('administrator.administrator')
               ->with(compact('destinations', 'users', 'roles', 'tickets_history', 'translations'));
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


    /**
     * Update translations files
     */
    public function updateLanguages(Request $request)
    {
        $translations = $request->input('translations', []);
        $localeBuffers = [];

        foreach ($translations as $key => $values) {
            foreach ($values as $locale => $value) {
                if (!isset($localeBuffers[$locale])) {
                    $path = base_path("lang/{$locale}.json");

                    $json = file_exists($path)
                        ? json_decode(file_get_contents($path), true)
                        : [];

                    $localeBuffers[$locale] = is_array($json) ? $json : [];
                }
                $localeBuffers[$locale][$key] = $value;
            }
        }
        
        foreach ($localeBuffers as $locale => $data) {
            $path = base_path("lang/{$locale}.json");

            file_put_contents(
                $path,
                json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Translations updated successfully!');
    }

}
