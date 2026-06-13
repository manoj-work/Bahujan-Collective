<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    protected $attributes = [
        'APPROVAL_STATUS' => 'pending',
    ];

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'EVENT_CATEGORY_ID', 'EVENT_CATEGORY_ID');
    }

    public function type()
    {
        return $this->belongsTo(EventType::class, 'EVENT_TYPE_ID', 'EVENT_TYPE_ID');
    }

    /**
     * Scope: only future and current (today) events. Past events are excluded.
     * Events with no START_DATE (date TBA) are included.
     * Uses config('app.timezone') for "today" (e.g. set to Asia/Kolkata for India).
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereDate('START_DATE', '>=', Carbon::today())
                ->orWhereNull('START_DATE');
        });
    }
}

