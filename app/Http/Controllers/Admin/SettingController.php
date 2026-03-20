<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'site_name'                  => Setting::get('site_name', 'Nhà Trọ'),
            'site_address'               => Setting::get('site_address', ''),
            'site_phone'                 => Setting::get('site_phone', ''),
            'site_email'                 => Setting::get('site_email', ''),
            'site_description'           => Setting::get('site_description', ''),
            'default_province'           => Setting::get('default_province', ''),
            'default_electricity_price'  => Setting::get('default_electricity_price', '3500'),
            'default_water_price'        => Setting::get('default_water_price', '15000'),
            'vietqr_bank_id'             => Setting::get('vietqr_bank_id', 'MB'),
            'vietqr_account_no'          => Setting::get('vietqr_account_no', ''),
            'momo_number'                => Setting::get('momo_number', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'                 => 'required|string|max:255',
            'site_address'              => 'nullable|string|max:500',
            'site_phone'                => 'nullable|string|max:50',
            'site_email'                => 'nullable|email|max:255',
            'site_description'          => 'nullable|string|max:1000',
            'default_province'          => 'nullable|string|max:100',
            'default_electricity_price' => 'required|numeric|min:0',
            'default_water_price'       => 'required|numeric|min:0',
            'vietqr_bank_id'            => 'nullable|string|max:50',
            'vietqr_account_no'         => 'nullable|string|max:50',
            'momo_number'               => 'nullable|string|max:20',
            'logo'                      => 'nullable|image|max:2048',
        ]);

        $keys = [
            'site_name', 'site_address', 'site_phone', 'site_email', 'site_description',
            'default_province',
            'default_electricity_price', 'default_water_price',
            'vietqr_bank_id', 'vietqr_account_no', 'momo_number',
        ];

        foreach ($keys as $key) {
            Setting::set($key, $request->input($key, ''));
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('settings', 'public');
            Setting::set('site_logo', $path);
        }

        return back()->with('success', 'Cập nhật cấu hình thành công!');
    }
}
