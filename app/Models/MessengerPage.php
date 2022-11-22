<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessengerPage extends Model
{
    use HasFactory;

    protected $table = 'messenger_pages';

    protected $fillable = [
        'uuid',
        'page_name',
        'page_id',
        'primary_page_token',
        'secondary_page_token',
        'app_webhook_url',
    ];
}
