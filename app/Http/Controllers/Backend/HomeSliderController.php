<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\HomeSlider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\HomeSliderRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class HomeSliderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $homeSliders = HomeSlider::paginate();

        return view('backend.home-slider.index', compact('homeSliders'))
            ->with('i', ($request->input('page', 1) - 1) * $homeSliders->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $homeSlider = new HomeSlider();

        return view('backend.home-slider.create', compact('homeSlider'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeSliderRequest $request): RedirectResponse
    {
        if ($request->hasFile('image')) {
            $icon = $request->file('image');
            $iconFileName = $icon->getClientOriginalName();

            $icon->move(public_path('site'), $iconFileName);

            $iconPath = 'site/' . $iconFileName;
            
        }
        HomeSlider::create([
            'heading' => $request->input('heading'),
            'sub_heading' => $request->input('sub_heading'),
            'image' => $iconPath
        ]);

        return Redirect::route('admin.home-sliders.index')
            ->with('success', 'HomeSlider created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $homeSlider = HomeSlider::find($id);

        return view('backend.home-slider.show', compact('homeSlider'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $homeSlider = HomeSlider::find($id);

        return view('backend.home-slider.edit', compact('homeSlider'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HomeSliderRequest $request, HomeSlider $homeSlider)
    {
        $industryId = $request->route('id');
        if ($request->hasFile('image')) {
            $icon = $request->file('image');
            $iconFileName = $icon->getClientOriginalName();
            $icon->move(public_path('site'), $iconFileName);
            if ($homeSlider->image) {
                $previousIconPath = public_path($homeSlider->image);
            
                if (file_exists($previousIconPath)) {
                    unlink($previousIconPath); // Delete previous icon file
                }
            }
            $homeSlider->image = 'site/' . $iconFileName;
            
        }

        $homeSlider->heading = $request->input('heading');
        $homeSlider->sub_heading = $request->input('sub_heading');

        $homeSlider->save();

        return Redirect::route('admin.home-sliders.index')
            ->with('success', 'HomeSlider updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $homeSlider = HomeSlider::find($id);
        if ($homeSlider->image) {
            $previousIconPath = public_path($homeSlider->image);
        
            if (file_exists($previousIconPath)) {
                unlink($previousIconPath); // Delete previous icon file
            }
        }
        $homeSlider->delete();
        return Redirect::route('admin.home-sliders.index')
            ->with('success', 'HomeSlider deleted successfully');
    }
}
