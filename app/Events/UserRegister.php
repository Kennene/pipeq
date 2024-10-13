<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegister implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct(private int $ticketId)
    {
        $this->message = "Twój bilet w kolejce to: " . $this->ticketId;
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
