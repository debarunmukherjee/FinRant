<?php

namespace App\Events;

use App\Models\Plan;
use App\Models\PlanUserInvite;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RejectInvite implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $invite;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(PlanUserInvite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn()
    {
        return new PrivateChannel("reject-invite." . $this->invite->sent_by);
    }

    public function broadcastWith(): array
    {
        $invitedFullName = User::getUserDetails($this->invite->sent_to)['full_name'];
        $planDetails = Plan::getPlanDetails($this->invite->plan_id);
        $planName = !empty($planDetails) ? $planDetails->name : '';
        return [
            'invitedName' => $invitedFullName,
            'planName' => $planName
        ];
    }
}
