<?php

namespace App\Events;

use App\Models\Plan;
use App\Models\PlanUserInvite;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewInvite implements ShouldBroadcastNow
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
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("plan-invite." . $this->invite->sent_to);
    }

    public function broadcastWith(): array
    {
        $inviterFullName = User::getUserDetails($this->invite->sent_by)['full_name'];
        $planDetails = Plan::getPlanDetails($this->invite->plan_id);
        $planName = !empty($planDetails) ? $planDetails->name : '';
        return [
            'inviterName' => $inviterFullName,
            'planName' => $planName,
            'totalInvites' => PlanUserInvite::getInviteCountOfUser($this->invite->sent_to)
        ];
    }
}
