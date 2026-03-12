<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::where('status', 'available')
            ->with('images')
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact('featuredRooms'));
    }
}
