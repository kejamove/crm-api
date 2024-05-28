<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Store;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'name',
        'location',
        'apartment_name',
        'area',
        'phone_number',
        'designation',
        'store',
        'lead'
    ];

    public function store()
    {
        return $this->belongsTo(Branch::class, 'store', 'id');
    }


}
