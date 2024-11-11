<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

use App\Models\Ticket;
use App\Models\TicketView;

class UpdateDisplayAboutTicket implements ShouldBroadcastNow
{
    use Dispatchable;

    public Ticket $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('display');
    }

    public function broadcastAs()
    {
        return 'UpdateDisplayAboutTicket';
    }

    public function broadcastWith()
    {
        return [
            'ticket' => TicketView::find($this->ticket->id)->toArray(),
        ];
    }
}
