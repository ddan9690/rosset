<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BenevolenceCategory extends Model
{
    protected $fillable = [
        'name',
        'amount',
    ];
}