<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\Tag;
use App\Models\Zone;
use App\Enums\EventType;
use Egulias\EmailValidator\Warning\Warning;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DependantLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public ?string $eventType = null;
    public string $finalMessage;

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
            $lowerZone = strtolower($activeZone->name);

            $this->eventType = match (true) {
                 str_contains($lowerZone, 'home') => 'ARRIVED_HOME',
                 str_contains($lowerZone, 'school') => 'ARRIVED_SCHOOL',
                 str_contains($lowerZone, 'sports') => 'CHECKED_IN_SPORTS',
                 str_contains($lowerZone, 'church') => 'CHECKED_IN_CHURCH',
                 str_contains($lowerZone, 'bus') => 'BOARDED_BUS',
                 str_contains($lowerZone, 'delay') => 'BUS_DELAY',
                default => 'UNKNOWN',
            };
            
            
            $isRestricted = ($activeZone->type === 'restricted');
            $notifType = $isRestricted ? 'danger' : 'success';
            $this->finalMessage = $isRestricted 
                ? "ALERT: {$dependant->name} entered a RESTRICTED zone: {$activeZone->name}!"
                : "Hello, {$dependant->name} has arrived at: {$activeZone->name}";
        } else {
            $this->zoneName = 'in transit';
            $this->eventType = 'BOARDED_BUS';
            $notifType = 'info';
            $this->finalMessage = "{$dependant->name} is currently in transit.";
        }


        $dependant->events()->create([
            'type' => $this->eventType,
            'location_name' => $this->zoneName,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timestamp' => now(),
        ]);

        Notification::create([
            'user_id' => $parent->id,
            'dependant_id' => $dependant->id,
            'dependant_name' => $dependant->name,
            'message' => $this->finalMessage,
            'is_read' => false,
            'type' => $notifType,
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
        $isDanger = str_contains(strtolower($this->eventType), 'danger') || $this->zoneName === 'RESTRICTED';

        return [
            'dependant_id' => $this->tag->dependant->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'zone_name' => $this->zoneName,
            'type' => $this->eventType,
            'timestamp' => now()->toDateTimeString(),

            'message' => $this->finalMessage,
            'category' => ($this->eventType === 'BUS_DELAY' || str_contains($this->finalMessage, 'ALERT')) ? 'alert' : 'info',
        ];
    }

    protected function generateBroadcastMessage(): string
    {
        $name = $this->tag->dependant->name;
        return match ($this->eventType) {
            EventType::ARRIVED_HOME->value => "Hello, {$name} has arrived home.",
            EventType::DEPARTED_HOME->value => "Update: {$name} has left home.",
            EventType::BOARDED_BUS->value => "{$name} is now in transit.",
            EventType::ARRIVED_SCHOOL->value => "{$name} has arrived at school.",
            EventType::LEFT_SCHOOL->value => "{$name} has left school.",
            EventType::CHECKED_IN_SPORTS->value => "{$name} checked in at sports facility.",
            EventType::BUS_DELAY->value => "Alert: {$name}'s bus is delayed.",
            default => "Update: {$name} is currently at {$this->zoneName}.",
        };
    }
}
