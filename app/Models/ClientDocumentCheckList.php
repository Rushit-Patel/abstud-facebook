<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientDocumentCheckList extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_id',
            'client_lead_id',
            'document_check_list_id',
            'document_type',
            'status',
            'notes',
            'meta_data',
            'added_by',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(DocumentCheckList::class, 'document_check_list_id');
    }

    public function documentUploads()
    {
        return $this->hasMany(ClientDocumentUpload::class, 'client_document_check_list_id');
    }

}
