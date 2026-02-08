<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    protected $table = 'securities';

    protected $fillable = [
        'username',
        'password',
    ];
}
