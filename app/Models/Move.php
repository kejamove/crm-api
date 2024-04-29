<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;


class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_source',
        'consumer_name',
        'corporate_name',
        'contact_information',
        'moving_from',
        'moving_to',
        'sales_representative',
        'invoiced_amount',
        'notes',
        'store',
    ];

    public function sales_representative()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

 
}
