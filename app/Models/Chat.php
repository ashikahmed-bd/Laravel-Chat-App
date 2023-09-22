<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // helper
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function isUnreadForUser($userId)
    {
        return (bool)$this->messages()
            ->whereNull('last_read_at')
            ->where('user_id', '<>', $userId)
            ->count();
    }

    public function markAsReadForUser($userId)
    {
        $this->messages()
            ->whereNull('last_read_at')
            ->where('user_id', '<>', $userId)
            ->update([
                'last_read_at' => Carbon::now()
            ]);
    }

    public function createParticipants($chatId, array $data)
    {
        $chat = Chat::find($chatId);
        $chat->participants()->sync($data);
    }


}
