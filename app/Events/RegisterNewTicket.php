<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use \App\Models\Ticket;
use \App\Models\TicketView;

class RegisterNewTicket implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private TicketView $ticket_view;

    /**
     * Create a new event instance.
     */
    public function __construct(private Ticket $ticket)
    {
        $this->ticket_view = TicketView::find($ticket->id)->forUser();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('register.' . $this->ticket->token);
    }

    public function broadcastWith()
    {
        return [
            'message' => "Successfully registered new ticket",
            'ticket' => $this->ticket_view
        ];
    }
}
