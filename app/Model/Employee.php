<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\Role; // Ensure you import the Role model
use App\User;

class Employee extends Model
{
    protected $fillable = [
        // old
        'name',
        'email',
        'phone',
        'nid',
        'joining_date',
        'photo',
        'role_id',
        'user_id',
        // new
        'first_name',
        'last_name',
        'id_number',
        'birthdate',
        'start_date',
        'pay_frequency',
        'payment_method',
        'status',
        'simplepay_employee_id',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'start_date' => 'date',
    ];

    // Backward-compat accessors (optional): make nid/name/joining_date reflect new fields
    public function getNidAttribute($v)
    {
        return $v ?: $this->attributes['id_number'] ?? null;
    }
    public function getNameAttribute($v)
    {
        return $v ?: trim(($this->attributes['first_name'] ?? '') . ' ' . ($this->attributes['last_name'] ?? ''));
    }
    public function getJoiningDateAttribute($v)
    {
        return $v ?: $this->attributes['start_date'] ?? null;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
