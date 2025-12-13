<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $primaryKey = 'EVENT_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    // Guard nothing for now; adjust as write operations are added
    protected $guarded = [];

    protected $casts = [
        'START_DATE' => 'date',
        'END_DATE' => 'date',
        'START_TIME' => 'datetime:H:i:s',
        'END_TIME' => 'datetime:H:i:s',
        'RSVP_COUNT' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'EVENT_CATEGORY_ID', 'EVENT_CATEGORY_ID');
    }

    public function type()
    {
        return $this->belongsTo(EventType::class, 'EVENT_TYPE_ID', 'EVENT_TYPE_ID');
    }
}

