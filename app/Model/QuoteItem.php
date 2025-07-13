<?php

namespace App\Model;

use App\Model\Quote;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
