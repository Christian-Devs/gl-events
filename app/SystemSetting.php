<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'company_name',
        'logo',
        'vat_number',
        'vat_rate',
        'currency',
        'email',
        'phone',
        'address',
        'footer_note'
    ];
}
