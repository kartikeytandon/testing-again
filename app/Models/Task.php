<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'task_title',
        'task_description',
        'task_date',
        'priority',
        'due_date',
        'remarks',
        'status',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'task_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
