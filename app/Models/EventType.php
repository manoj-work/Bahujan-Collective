<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $table = 'event_types';

    protected $primaryKey = 'EVENT_TYPE_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];
}

