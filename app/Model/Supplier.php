<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'company_name',
        'vat_number',
        'phone',
        'email',
        'address',
        'notes',
        'contact_person'
    ];

}

