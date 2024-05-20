<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Firm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'registration_number',
    ];

    public function branches()
    {
        return $this->hasMany(Branch::class, 'firm', 'id');
    }
}
