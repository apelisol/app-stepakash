<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outbox extends Model
{
    protected $fillable = ['receiver', 'message', 'status', 'created_on'];
    
}
