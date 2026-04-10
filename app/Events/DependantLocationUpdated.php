<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\Tag;
use App\Models\Zone;
use Egulias\EmailValidator\Warning\Warning;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DependantLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Tag $tag,
        public float $latitude,
        public float $longitude,
        public ?string $zoneName = null,
    )
    {
        $dependant = $this->tag->dependant;
        $parent = $dependant->user;

        $activeZone = $parent->zones->first(fn($zone) => $zone->isInside($this->latitude, $this->longitude));

        if ($activeZone) {
            $this->zoneName = $activeZone->name;
            // Check if it's a restricted zone to set the alert level
            $isRestricted = ($activeZone->type === 'restricted');
            
            $type = $isRestricted ? 'danger' : 'success';
            $message = $isRestricted 
                ? "🚨 ALERT: {$dependant->name} entered a RESTRICTED zone: {$activeZone->name}!"
                : "Hello, {$dependant->name} has arrived at: {$activeZone->name}";
        } else {
            $this->zoneName = 'an unknown location';
            $type = 'info';
            $message = "{$dependant->name} is currently at an unknown location.";
        }

        $message = "Hello, {$dependant->name} has arrived at:  {$this->zoneName}";
        if ($activeZone && $activeZone->is_restricted) {
            $message .= " Warning: {$dependant->name} has entered a restricted area!";
        }

        Notification::create([
            'user_id' => $parent->id,
            'dependant_name' => $dependant->name,
            'message' => "$message",
            'is_read' => false,
            'type' => '$type',
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('dependant.' . $this->tag->dependant->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'dependant_id' => $this->tag->dependant->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'zone_name' => $this->zoneName,
           // 'zone_type' => $this->zoneType,
        ];
    }
}
