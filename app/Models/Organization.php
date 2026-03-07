<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $primaryKey = 'ORG_ID';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];
}





