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

    public static function submissionRules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:10',
        ];
    }

    public static function createFromSubmission(array $data): self
    {
        return self::create([
            'NAME' => $data['name'],
            'EMAIL' => $data['email'],
            'MESSAGE' => $data['message'],
        ]);
    }
}
