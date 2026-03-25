<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredRooms = Room::where('status', 'available')
            ->approved()
            ->with('images')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->take(6)
            ->get();

        $sliders = Slider::active()->get();

        return view('home', compact('featuredRooms', 'sliders'));
    }
}
