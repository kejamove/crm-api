<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSetup extends Model
{
    use HasFactory;

    protected $fillable = [
        'host',
        'mailer',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'firm',
    ];

    public function firm(){
        return $this->belongsTo(Firm::class, 'firm', 'id');
    }
}
