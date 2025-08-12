<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientDocumentUpload extends Model
{
    use HasFactory, SoftDeletes;

        protected $fillable = [
            'client_document_check_list_id',
            'document_name',
            'document_path',
            'status',
        ];
    protected $casts = [
        'deleted_at' => 'datetime',
    ];

}
