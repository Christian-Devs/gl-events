<?php

namespace App\Model;

use App\Model\QuoteItem;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = [
        'client_name',
        'client_email',
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
}
