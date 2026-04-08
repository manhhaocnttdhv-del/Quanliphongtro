<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone'          => ['required', 'string', 'max:20'],
            'password'       => ['required', 'confirmed', Rules\Password::defaults()],
            'role'           => ['required', 'in:landlord,tenant'],
            'id_card'        => ['nullable', 'string', 'max:20'],
            'dob'            => ['nullable', 'date'],
            'gender'         => ['nullable', 'string'],
            'province_name'  => ['required', 'string'],
            'district_name'  => ['required', 'string'],
            'ward_name'      => ['nullable', 'string'],
            'address_detail' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'id_card'        => $request->id_card,
            'dob'            => $request->dob,
            'gender'         => $request->gender,
            'province_name'  => $request->province_name,
            'district_name'  => $request->district_name,
            'ward_name'      => $request->ward_name,
            'address_detail' => $request->address_detail,
            'password'       => Hash::make($request->password),
            'role'           => $request->role,
        ]);

        event(new Registered($user));
        Auth::login($user);

        if ($user->isLandlord()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('rooms.index');
    }
}
