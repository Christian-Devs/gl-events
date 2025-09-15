<?php

namespace App\Model;

use App\Model\QuoteItem;
use App\Model\Jobcard;
use App\Model\Invoice;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'client_name',
        'client_email',
        'quote_number',
        'quote_date',
        'status',
        'subtotal',
        'vat',
        'total',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
    public function jobcard()
    {
        return $this->hasOne(Jobcard::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}
