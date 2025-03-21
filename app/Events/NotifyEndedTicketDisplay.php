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

class NotifyEndedTicketDisplay implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private TicketView $ticket_view;

    /**
     * Create a new event instance.
     */
    public function __construct(private Ticket $ticket, private ?string $message = null)
    {
        $this->ticket_view = TicketView::find($ticket->id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('display');
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message ?? 'The following ticket has been closed',
            'ticket' => $this->ticket
        ];
    }
}
