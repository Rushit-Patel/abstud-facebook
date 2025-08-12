<?php

// Model 1: TeamNotificationType.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamNotificationType extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_key',
        'title',
        'description',
        'icon',
        'color',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function notifications()
    {
        return $this->hasMany(TeamNotification::class, 'notification_type_id');
    }

    // Method to process template with variables
    public function processTemplate($variables = [])
    {
        $message = $this->description;
        
        foreach ($variables as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
            $message = str_replace('{ ' . $key . ' }', $value, $message);
            $message = str_replace('{{ ' . $key . ' }}', $value, $message);
            $message = str_replace('{{' . $key . '}}', $value, $message);
        }
        
        return $message;
    }
}