<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'amount',
        'payment_method',
        'payment_date',
        'status',
        'notes',
        'invoice_id',
        'jobcard_id'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function jobcard()
    {
        return $this->belongsTo(Jobcard::class);
    }
}
