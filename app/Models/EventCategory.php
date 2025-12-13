<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    use HasFactory;

    protected $table = 'event_categories';

    protected $primaryKey = 'EVENT_CATEGORY_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];
}

