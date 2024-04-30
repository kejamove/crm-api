<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Move;

class Volume extends Model
{
    use HasFactory;

    protected $fillable = [
        'move_id',
        'area',
        'item',
        'size_cubic_meters',
        'quantity',
        'number_of_boxes',
    ];

    /**
     * Get the move that owns the volume.
     */
    public function move()
    {
        return $this->belongsTo(Move::class);
    }
}
