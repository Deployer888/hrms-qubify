<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\HomeRoadmap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\HomeRoadmapRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class HomeRoadmapController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $homeRoadmaps = HomeRoadmap::paginate();
        // dd($homeRoadmaps);
        return view('backend.home-roadmap.index', compact('homeRoadmaps'))
            ->with('i', ($request->input('page', 1) - 1) * $homeRoadmaps->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $homeRoadmap = new HomeRoadmap();

        return view('backend.home-roadmap.create', compact('homeRoadmap'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HomeRoadmapRequest $request): RedirectResponse
    {
        if ($request->hasFile('image')) {
            $icon = $request->file('image');
            $iconFileName = $icon->getClientOriginalName();

            $icon->move(public_path('site'), $iconFileName);

            $iconPath = 'site/' . $iconFileName;
            
        }

        HomeRoadmap::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'image' => $iconPath
        ]);
        HomeRoadmap::create($request->validated());

        return Redirect::route('admin.home-roadmaps.index')
            ->with('success', 'HomeRoadmap created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $homeRoadmap = HomeRoadmap::find($id);

        return view('backend.home-roadmap.show', compact('homeRoadmap'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $homeRoadmap = HomeRoadmap::find($id);

        return view('backend.home-roadmap.edit', compact('homeRoadmap'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(HomeRoadmapRequest $request, HomeRoadmap $homeRoadmap): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            $icon->move(public_path('site'), $iconFileName);
            if ($homeRoadmap->image) {
                $previousIconPath = public_path($homeRoadmap->icon);
            
                if (file_exists($previousIconPath)) {
                    unlink($previousIconPath); // Delete previous icon file
                }
            }
            $homeRoadmap->icon = 'site/' . $iconFileName;
            
        }

        $homeRoadmap->title = $request->input('title');
        $homeRoadmap->description = $request->input('description');

        $homeRoadmap->save();

        return Redirect::route('admin.home-roadmaps.index')
            ->with('success', 'HomeRoadmap updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        HomeRoadmap::find($id)->delete();

        return Redirect::route('backend.home-roadmaps.index')
            ->with('success', 'HomeRoadmap deleted successfully');
    }
}
