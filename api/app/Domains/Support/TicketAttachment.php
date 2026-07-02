<?php

namespace App\Domains\Support;

use App\Domains\Identity\User;
use Illuminate\Database\Eloquent\Model;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'ticket_comment_id',
        'user_id',
        'file_name',
        'file_path',
        'mime_type',
        'size',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function comment()
    {
        return $this->belongsTo(TicketComment::class, 'ticket_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
