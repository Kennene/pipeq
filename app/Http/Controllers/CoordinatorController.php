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
    public function index(Request $request): View
    {
        $color = new Color();
        $tickets = TicketView::all();
        $statuses = Status::allTranslated();
        $destinations = Destination::with('workstations')->get();

        // workstations są również w destinations, wiec możesz je stamtąd wyjąć.
        // jeżeli potrzebujesz je osobno, np. dla czytelności kodu, odkomentuj poniższą linię i dorzuć do compact
        // $workstations = Workstation::all();


        //* dorzuciłem do statuses tłumaczenia. użyj proszę zmiennej $statuses zamiast $variables["translations"]
            $variables["translations"] = [
                'statuses' => [
                    '1' => __('statuses.1.name'),
                    '2' => __('statuses.2.name'),
                    '3' => __('statuses.3.name'),
                    '4' => __('statuses.4.name'),
                ]
            ];


        return view('coordinator.coordinator')->with(compact('color', 'tickets', 'statuses', 'destinations'))

            // todo: usunąć poniższe, lepiej to robić przez compact.
            // można wtedy łatwiej wyjąć dane javascriptem poprzez @json($statuses);
            ->with($variables);
    }

    // jeżeli chcesz coś zmienić lub podejrzeć w przepływie to przeniosłem to do kontrollera TicketController
}
