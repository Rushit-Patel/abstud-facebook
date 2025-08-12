<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class MockTest extends Model
{
    use HasFactory, SoftDeletes;
     protected $fillable = [
        'name',
        'mock_test_date',
        'mock_test_time',
        'coaching_id',
        'batch_id',
        'branch_id',
        'remarks',
        'status',
        'added_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function getCoaching()
    {
        return $this->belongsTo( Coaching::class, 'coaching_id', 'id');
    }
    public function getBranch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function getBatch()
    {
        return $this->belongsTo(Batch::class, 'batch_id', 'id');
    }
}
