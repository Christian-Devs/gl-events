<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use App\Role; // Ensure you import the Role model
use App\User;

class Employee extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'joining_date',
        'nid',
        'photo',
        'role_id', // Add this
        'user_id', // Optional, if you're linking employee to user table
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
