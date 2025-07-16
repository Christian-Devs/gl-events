<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'quote_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'subtotal',
        'vat',
        'total',
        'status',
        'notes'
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
