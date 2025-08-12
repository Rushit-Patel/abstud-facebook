<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'class',
        'email_enabled',
        'email_template_id',
        'whatsapp_enabled',
        'whatsapp_template',
        'system_enabled',
        'team_notification_types',
    ];

    public function emailTemplate()
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function teamNotificationType()
    {
        return $this->belongsTo(TeamNotificationType::class, 'team_notification_types');
    }
}
