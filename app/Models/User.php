<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function moduleAccess()
    {
        return $this->hasMany(ModuleAccess::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function ledgerRequests()
    {
        return $this->hasMany(LedgerRequest::class, 'client_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function salesReports()
    {
        return $this->hasMany(SalesReport::class, 'sales_person_id');
    }

    public function clientSalesReports()
    {
        return $this->hasMany(SalesReport::class, 'client_id');
    }

    // Helper methods
    public function hasModuleAccess($moduleName, $permission = null)
    {
        // Admin has implicit access to all modules and permissions
        if ($this->isAdmin()) {
            return true;
        }
        $access = $this->moduleAccess()
            ->where('module_name', $moduleName)
            ->where('is_active', true)
            ->first();

        if (!$access) {
            return false;
        }

        if ($permission) {
            return in_array($permission, $access->permissions);
        }

        return true;
    }

    public function getModuleAccessAttribute()
    {
        // Admin: expose all modules
        if ($this->isAdmin()) {
            return [
                'orders',
                'ledger_requests',
                'tasks',
                'attendance',
                'sales_reports',
                'user_management',
                'dashboard',
            ];
        }
        if (!$this->relationLoaded('moduleAccess')) {
            $this->load('moduleAccess');
        }
        
        return $this->moduleAccess()
            ->where('is_active', true)
            ->pluck('module_name')
            ->toArray();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isSalesPerson()
    {
        return $this->role === 'sales_person';
    }

    public function isOfficeStaff()
    {
        return $this->role === 'office_staff';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }
}
