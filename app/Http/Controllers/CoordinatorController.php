<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Color;

use App\Models\Ticket;
use App\Models\TicketView;
use App\Models\Status;
use App\Models\Destination;
use App\Models\Workstation;

class CoordinatorController extends Controller
{
    // app/Http/Controllers/CoordinatorController.php

    public function index(Request $request): View
    {
        $color = new Color();
        $tickets = TicketView::all();
        $statuses = Status::allTranslated();
        $destinations = Destination::with('workstations')->get();

        // Translate all destinations and workstations
        foreach ($destinations as $destination) {
            $destination->translate();
            foreach ($destination->workstations as $workstation) {
                $workstation->translate();
            }
        }

        // Przygotowanie tłumaczeń statusów
        $translations = [
            'statuses' => [
                '1' => __('statuses.1.name'),
                '2' => __('statuses.2.name'),
                '3' => __('statuses.3.name'),
                '4' => __('statuses.4.name'),
            ]
        ];

        // Przekazanie wszystkich danych za pomocą compact
        return view('coordinator.coordinator')->with(compact('color', 'tickets', 'statuses', 'destinations', 'translations'));
    }
}
