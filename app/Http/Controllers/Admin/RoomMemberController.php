<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Room;
use App\Models\Contract;
use App\Models\RoomMember;
use Illuminate\Support\Facades\Storage;

class RoomMemberController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'id_card_number' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'id_card_front' => 'nullable|image|max:2048',
            'id_card_back' => 'nullable|image|max:2048',
        ]);

        $room = Room::findOrFail($request->room_id);
        
        // Permission check
        if (auth()->user()->isLandlord() && $room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->all();
        
        if ($request->hasFile('id_card_front')) {
            $data['id_card_front'] = $request->file('id_card_front')->store('members/id_cards', 'public');
        }
        
        if ($request->hasFile('id_card_back')) {
            $data['id_card_back'] = $request->file('id_card_back')->store('members/id_cards', 'public');
        }

        RoomMember::create($data);

        return back()->with('success', 'Thêm thành viên mới thành công.');
    }

    public function update(Request $request, RoomMember $roomMember)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'id_card_number' => 'nullable|string|max:20',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'id_card_front' => 'nullable|image|max:2048',
            'id_card_back' => 'nullable|image|max:2048',
        ]);

        // Permission check
        if (auth()->user()->isLandlord() && $roomMember->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->all();

        if ($request->hasFile('id_card_front')) {
            if ($roomMember->id_card_front) Storage::disk('public')->delete($roomMember->id_card_front);
            $data['id_card_front'] = $request->file('id_card_front')->store('members/id_cards', 'public');
        }

        if ($request->hasFile('id_card_back')) {
            if ($roomMember->id_card_back) Storage::disk('public')->delete($roomMember->id_card_back);
            $data['id_card_back'] = $request->file('id_card_back')->store('members/id_cards', 'public');
        }

        $roomMember->update($data);

        return back()->with('success', 'Cập nhật thông tin thành viên thành công.');
    }

    public function destroy(RoomMember $roomMember)
    {
        // Permission check
        if (auth()->user()->isLandlord() && $roomMember->room->landlord_id !== auth()->id()) {
            abort(403);
        }

        if ($roomMember->id_card_front) Storage::disk('public')->delete($roomMember->id_card_front);
        if ($roomMember->id_card_back) Storage::disk('public')->delete($roomMember->id_card_back);

        $roomMember->delete();

        return back()->with('success', 'Xóa thành viên thành công.');
    }
}
