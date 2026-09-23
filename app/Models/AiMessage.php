<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['role', 'content'])]
class AiMessage extends Model
{
    /** @use HasFactory<\Database\Factories\AiMessageFactory> */
    use HasFactory;

    public function chat()
    {
        return $this->belongsTo(AiChat::class, 'ai_chat_id');
    }
}
