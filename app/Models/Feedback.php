<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';

    protected $primaryKey = 'FEEDBACK_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

    protected $fillable = [
        'NAME',
        'EMAIL',
        'MESSAGE',
    ];
}

