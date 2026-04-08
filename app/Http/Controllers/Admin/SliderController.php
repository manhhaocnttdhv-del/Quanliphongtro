<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order')->orderBy('id')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'subtitle'  => 'nullable|string|max:500',
            'image'     => 'required|image|max:4096',
            'link'      => 'nullable|url|max:255',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $path = $request->file('image')->store('sliders', 'public');

        Slider::create([
            'title'      => $request->title,
            'subtitle'   => $request->subtitle,
            'image_path' => $path,
            'link'       => $request->link,
            'order'      => $request->order ?? 0,
            'is_active'  => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Đã thêm slide mới!');
    }

    public function edit(Slider $slider)
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $request->validate([
            'title'     => 'nullable|string|max:255',
            'subtitle'  => 'nullable|string|max:500',
            'image'     => 'nullable|image|max:4096',
            'link'      => 'nullable|url|max:255',
            'order'     => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = [
            'title'     => $request->title,
            'subtitle'  => $request->subtitle,
            'link'      => $request->link,
            'order'     => $request->order ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            // Xóa ảnh cũ
            Storage::disk('public')->delete($slider->image_path);
            $data['image_path'] = $request->file('image')->store('sliders', 'public');
        }

        $slider->update($data);

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Đã cập nhật slide!');
    }

    public function destroy(Slider $slider)
    {
        Storage::disk('public')->delete($slider->image_path);
        $slider->delete();

        return redirect()->route('admin.sliders.index')
            ->with('success', 'Đã xóa slide!');
    }

    public function toggleActive(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);
        $status = $slider->is_active ? 'bật' : 'tắt';
        return back()->with('success', "Slide đã được $status!");
    }
}
