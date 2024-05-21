<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'firm',
    ];

    public function employees()
    {
        return $this->hasMany(User::class, 'branch', 'id');
    }

    public function moves()
    {
        return $this->hasMany(Move::class, 'branch', 'id');
    }

    public function firm()
    {
        return $this->belongsTo(Firm::class, 'firm', 'id');
    }


}
