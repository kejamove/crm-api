<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'client_email',
        'invoice_amount',
        'invoice_status',
        'invoice_number',
        'move',
    ];


    public function move()
    {
        return $this->belongsTo(Move::class, 'move', 'id');
    }


}

