<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'postal',
        'address',
        'building',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
