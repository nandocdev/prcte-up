<?php

namespace App\Events;

use App\Models\WorkMessage;
use App\Models\WorkOfExtension;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkMessage $message;
    public WorkOfExtension $work;
    public User $sender;
    public User $recipient;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkMessage $message, WorkOfExtension $work, User $sender, User $recipient)
    {
        $this->message = $message;
        $this->work = $work;
        $this->sender = $sender;
        $this->recipient = $recipient;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('work-messages.' . $this->recipient->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'created_at' => $this->message->created_at->toISOString(),
                'sender' => [
                    'id' => $this->sender->id,
                    'name' => $this->sender->name,
                ],
                'work' => [
                    'id' => $this->work->id,
                    'title' => $this->work->title,
                ],
            ],
        ];
    }
}
