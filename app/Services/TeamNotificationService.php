<?php
namespace App\Services;

use App\Models\TeamNotification;
use App\Models\TeamNotificationType;
use App\Events\NotificationCreated;

class TeamNotificationService
{
    /**
     * Create a new notification
     */
    public function create($typeKey, $userId, $variables = [], $link = null, $createdBy = null)
    {
        // Get notification type
        $notificationType = TeamNotificationType::where('type_key', $typeKey)
            ->where('is_active', true)
            ->first();

        if (!$notificationType) {
            throw new \Exception("Notification type '{$typeKey}' not found or inactive");
        }

        // Process template with variables
        $message = $notificationType->processTemplate($variables);

        // Create notification
        $notification = TeamNotification::create([
            'notification_type_id' => $notificationType->id,
            'title' => $notificationType->title,
            'message' => $message,
            'link' => $link,
            'data' => $variables,
            'user_id' => $userId,
            'created_by' => $createdBy ?? auth()->id(),
        ]);

        // Fire event for real-time updates
        event(new NotificationCreated($notification));

        return $notification;
    }

    /**
     * Create notification for multiple users
     */
    public function createForUsers($typeKey, $userIds, $variables = [], $link = null, $createdBy = null)
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = $this->create($typeKey, $userId, $variables, $link, $createdBy);
        }

        return $notifications;
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 10)
    {
        return TeamNotification::forUser($userId)
            ->with(['notificationType', 'creator'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get unseen count for user
     */
    public function getUnseenCount($userId)
    {
        return TeamNotification::forUser($userId)
            ->unseen()
            ->count();
    }

    /**
     * Mark notification as seen
     */
    public function markAsSeen($notificationId, $userId)
    {
        $notification = TeamNotification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            $notification->markAsSeen();
            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as seen for user
     */
    public function markAllAsSeen($userId)
    {
        return TeamNotification::forUser($userId)
            ->unseen()
            ->update([
                'is_seen' => true,
                'seen_at' => now()
            ]);
    }

    /**
     * Delete old notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        return TeamNotification::where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}

// Event: NotificationCreated.php
namespace App\Events;

use App\Models\TeamNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;

    public function __construct(TeamNotification $notification)
    {
        $this->notification = $notification->load(['notificationType', 'creator']);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->notification->user_id);
    }

    public function broadcastAs()
    {
        return 'notification.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'link' => $this->notification->link,
            'icon' => $this->notification->notificationType->icon,
            'color' => $this->notification->notificationType->color,
            'created_at' => $this->notification->created_at->toISOString(),
            'creator_name' => $this->notification->creator->name ?? 'System',
        ];
    }
}