<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Store;
use App\Models\Lead;


class Move extends Model
{
    use HasFactory;

    protected $fillable = [
        'move_stage',
        'consumer_name',
        'corporate_name',
        'contact_information',
        'moving_from',
        'moving_to',
        'sales_representative',
        'invoiced_amount',
        'notes',
        'remarks',
        'branch',
        'lead_source',
        'move_request_received_at',
    ];

    public function sales_representative()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }


}
