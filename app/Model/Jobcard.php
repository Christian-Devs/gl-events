<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Jobcard extends Model
{
    protected $fillable = [
        'job_number', 'salesperson', 'stand_name', 'show_name', 'materials', 'total_amount'
    ];
}
