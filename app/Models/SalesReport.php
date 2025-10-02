<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_person_id',
        'client_id',
        'order_id',
        'visit_date',
        'order_value',
        'remarks',
        'visit_duration',
        'visit_purpose',
        'client_feedback',
        'follow_up_required',
        'follow_up_date',
        'visit_photo_path',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'follow_up_date' => 'date',
            'order_value' => 'decimal:2',
            'follow_up_required' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function salesPerson()
    {
        return $this->belongsTo(User::class, 'sales_person_id');
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
