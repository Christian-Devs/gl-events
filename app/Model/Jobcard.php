<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Jobcard extends Model
{
    protected $fillable = [
        'quote_id',
        'assigned_to',
        'status',
        'start_date',
        'due_date',
        'notes',
    ];

    public function quote()
    {
        return $this->belongsTo(\App\Model\Quote::class);
    }
}
