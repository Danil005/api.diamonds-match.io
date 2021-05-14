<?php

namespace App\Events;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use JetBrains\PhpStorm\Pure;

class NotifyPushed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private string $date = '';
    private string $message = '';
    private mixed $payload = [];

    public function __construct(string $message, mixed $payload = [])
    {
        $this->date = Carbon::now()->format('d.m.Y H:i');
        $this->message = $message;
        $this->payload = $payload;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    #[Pure] public function broadcastOn(): Channel
    {
        return new Channel('notify');
    }
}
