<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappTemplateVariableMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'whatsapp_variable',
        'system_variable',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user who created this mapping
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all mappings for a specific template
     */
    public static function getMappingsForTemplate(string $templateName): array
    {
        return self::where('template_name', $templateName)
            ->pluck('system_variable', 'whatsapp_variable')
            ->toArray();
    }

    /**
     * Save mappings for a template (replace existing)
     */
    public static function saveMappingsForTemplate(string $templateName, array $mappings, ?int $userId = null): void
    {
        // Delete existing mappings for this template
        self::where('template_name', $templateName)->delete();

        // Insert new mappings
        $data = [];
        foreach ($mappings as $whatsappVar => $systemVar) {
            if (!empty($systemVar)) {
                $data[] = [
                    'template_name' => $templateName,
                    'whatsapp_variable' => $whatsappVar,
                    'system_variable' => $systemVar,
                    'created_by' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (!empty($data)) {
            self::insert($data);
        }
    }

    /**
     * Get unique template names that have mappings
     */
    public static function getTemplatesWithMappings(): array
    {
        return self::distinct('template_name')
            ->pluck('template_name')
            ->toArray();
    }
}
