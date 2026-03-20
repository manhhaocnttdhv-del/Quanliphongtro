<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomReview;
use Illuminate\Http\Request;

class RoomReviewController extends Controller
{
    public function store(Request $request, Room $room)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:100',
            'comment' => 'required|string|min:10|max:1000',
        ], [
            'rating.required'  => 'Vui lòng chọn số sao đánh giá.',
            'comment.required' => 'Vui lòng nhập nội dung bình luận.',
            'comment.min'      => 'Bình luận phải có ít nhất 10 ký tự.',
        ]);

        // Check if already reviewed
        $existing = RoomReview::where('room_id', $room->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            // Update existing review
            $existing->update([
                'rating'  => $request->rating,
                'title'   => $request->title,
                'comment' => $request->comment,
            ]);
            return back()->with('success', 'Đã cập nhật đánh giá của bạn!');
        }

        RoomReview::create([
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'rating'  => $request->rating,
            'title'   => $request->title,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }

    public function destroy(RoomReview $review)
    {
        if ($review->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $room = $review->room;
        $review->delete();

        return back()->with('success', 'Đã xóa đánh giá.');
    }
}
