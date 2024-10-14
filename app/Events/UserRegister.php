<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;

class UserRegister implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(private Ticket $ticket)
    {
        $this->message = "Twój bilet w kolejce to: " . $ticket->id % 100;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        //! Dlaczego event przesyłany jest do wszystkich użytkowników, a nie tylko do jednego?
        // todo: dodać autoryzację do kanału w channels.php
        return [
            new PrivateChannel('register'),
        ];
    }
}
