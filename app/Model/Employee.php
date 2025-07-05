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
        'nid',
        'joining_date',
        'photo',
        'role_id',
        'user_id',
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
