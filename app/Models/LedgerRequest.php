<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'request_date',
        'status',
        'ledger_file_path',
        'admin_remarks',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
}
