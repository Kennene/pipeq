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

class UpdateUserAboutHisTicket implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TicketView $ticket_view;

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
        return new PrivateChannel('register.' . $this->ticket->user_id);
    }

    // todo: user doesn't need his entire ticket. limit the data sent
    public function broadcastWith()
    {
        return [
            'message' => $this->message ?? 'Your ticket has been updated',
            'ticket' => $this->ticket_view
        ];
    }
}