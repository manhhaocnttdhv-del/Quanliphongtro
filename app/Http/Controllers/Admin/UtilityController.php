<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Utility;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Http\Request;

class UtilityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Utility::with('room');

        if ($user->isLandlord()) {
            $query->whereHas('room', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $utilities = $query->orderByDesc('year')->orderByDesc('month')->paginate(15)->withQueryString();
        $rooms = Room::when($user->isLandlord(), fn($q) => $q->where('landlord_id', $user->id))->orderBy('name')->get();

        return view('admin.utilities.index', compact('utilities', 'rooms'));
    }

    public function create()
    {
        $user = auth()->user();
        $rooms = Room::where('status', 'rented')
            ->when($user->isLandlord(), fn($q) => $q->where('landlord_id', $user->id))
            ->orderBy('name')->get();
        $defaultElecPrice = Setting::get('default_electricity_price', 3500);
        $defaultWaterPrice = Setting::get('default_water_price', 15000);

        return view('admin.utilities.create', compact('rooms', 'defaultElecPrice', 'defaultWaterPrice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id'          => 'required|exists:rooms,id',
            'month'            => 'required|integer|between:1,12',
            'year'             => 'required|integer|min:2020',
            'electricity_old'  => 'required|numeric|min:0',
            'electricity_new'  => 'required|numeric|gte:electricity_old',
            'water_old'        => 'required|numeric|min:0',
            'water_new'        => 'required|numeric|gte:water_old',
            'electricity_price'=> 'required|numeric|min:0',
            'water_price'      => 'required|numeric|min:0',
        ]);

        $room = Room::findOrFail($request->room_id);
        if (auth()->user()->isLandlord() && $room->landlord_id !== auth()->id()) {
            abort(403);
        }

        $electricityAmount = ($request->electricity_new - $request->electricity_old) * $request->electricity_price;
        $waterAmount       = ($request->water_new - $request->water_old) * $request->water_price;

        Utility::updateOrCreate(
            ['room_id' => $request->room_id, 'month' => $request->month, 'year' => $request->year],
            [
                'electricity_old'    => $request->electricity_old,
                'electricity_new'    => $request->electricity_new,
                'water_old'          => $request->water_old,
                'water_new'          => $request->water_new,
                'electricity_price'  => $request->electricity_price,
                'water_price'        => $request->water_price,
                'electricity_amount' => $electricityAmount,
                'water_amount'       => $waterAmount,
            ]
        );

        return redirect()->route('admin.utilities.index')
            ->with('success', 'Đã lưu chỉ số điện nước thành công!');
    }
}
