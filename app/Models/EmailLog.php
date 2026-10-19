<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_log';

    public $timestamps = false;

    protected $fillable = [
        'date',
        'from',
        'to',
        'cc',
        'bcc',
        'subject',
        'body',
        'headers',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }
}
