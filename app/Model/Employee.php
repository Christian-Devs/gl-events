<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\Role; // Ensure you import the Role model
use App\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'id_number',
        'birthdate',
        'start_date',
        'pay_frequency',
        'payment_method',
        'status',
        'simplepay_employee_id',
        'external_reference',
        'role_id',
        'user_id',
        'bank_id',
        'bank_account_type',
        'bank_account_number',
        'bank_branch_code',
        'bank_holder_relationship',
        'bank_holder_name',
    ];

    protected $casts = [
        'birthdate'  => 'date',
        'start_date' => 'date',
    ];

    // Helpers
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
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
