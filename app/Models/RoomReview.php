<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomReview extends Model
{
    protected $fillable = ['room_id', 'user_id', 'rating', 'title', 'comment'];

    protected $casts = ['rating' => 'integer'];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Render stars as HTML
    public function starsHtml(): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $this->rating
                ? '<i class="fa fa-star text-warning"></i>'
                : '<i class="fa fa-star-o text-muted"></i>';
        }
        return $html;
    }
}
