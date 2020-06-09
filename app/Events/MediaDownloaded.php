<?php

declare(strict_types=1);

namespace App\Events;

use App\Media;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaDownloaded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected Media $media;

    /**
     * Create a new event instance.
     *
     * @param \App\Media $media
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function broadcastWith(): array
    {
        return ['download' => $this->media->getDownloadUrl()];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('media-downloaded.' . $this->media->id);
    }
}
