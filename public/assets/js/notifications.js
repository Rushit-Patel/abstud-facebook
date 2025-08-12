/**
 * Notification Management jQuery Functions
 */

$(document).ready(function() {
    
    // Mark single notification as read
    window.markAsRead = function(notificationId) {
        $.ajax({
            url: `/team/notifications/${notificationId}/mark-read`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    // Update notification appearance
                    const $notification = $(`#notification_request_${notificationId}`);
                    if ($notification.length) {
                        $notification.css('opacity', '0.5');
                        // Remove mark as read button
                        $notification.find('button[onclick*="markAsRead"]').remove();
                    }
                    // Update notification count
                    updateNotificationCount();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking notification as read:', error);
            }
        });
    };

    // Mark all notifications as read
    window.markAllAsRead = function(type = 'all') {
        $.ajax({
            url: '/team/notifications/mark-all-read',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({ type: type }),
            contentType: 'application/json',
            success: function(data) {
                if (data.success) {
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error marking all notifications as read:', error);
            }
        });
    };

    // Update notification count in header
    window.updateNotificationCount = function() {
        $.ajax({
            url: '/team/notifications/count',
            type: 'GET',
            success: function(data) {
                if (data.success) {
                    const $badge = $('.kt-badge');
                    if ($badge.length) {
                        $badge.text(data.count);
                        if (data.count === 0) {
                            $badge.parent().hide();
                        } else {
                            $badge.parent().show();
                        }
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating notification count:', error);
            }
        });
    };

    // Auto-refresh notification count every 30 seconds
    setInterval(function() {
        updateNotificationCount();
    }, 30000);

});
