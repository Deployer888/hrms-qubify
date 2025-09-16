<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\{TechnologyList,Technology};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TechnologyListRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Auth;

class TechnologyListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $technologyLists = TechnologyList::paginate();

        return view('backend.technology-list.index', compact('technologyLists'))
            ->with('i', ($request->input('page', 1) - 1) * $technologyLists->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $technologyList = new TechnologyList();
        $types = Technology::all();
        return view('backend.technology-list.create', compact('technologyList','types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TechnologyListRequest $request): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();

            $icon->move(public_path('images/techimages'), $iconFileName);

            $iconPath = 'images/techimages/' . $iconFileName;
            
        }
        TechnologyList::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'technology_id' => $request->input('technology_id'),
            'icon' => $iconPath
        ]);

        return Redirect::route('admin.technology-lists.index')
            ->with('success', 'TechnologyList created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $technologyList = TechnologyList::find($id);

        return view('backend.technology-list.show', compact('technologyList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $technologyList = TechnologyList::find($id);
        $types = Technology::all();
        return view('backend.technology-list.edit', compact('technologyList','types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TechnologyListRequest $request, TechnologyList $technologyList): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            $icon->move(public_path('images/techimages'), $iconFileName);
            if ($technologyList->icon) {
                $previousIconPath = public_path($technologyList->icon);
            
                if (file_exists($previousIconPath)) {
                    unlink($previousIconPath); // Delete previous icon file
                }
            }
            $technologyList->icon = 'images/techimages/' . $iconFileName;
            
        }

        $technologyList->name = $request->input('name');
        $technologyList->description = $request->input('description');
        $technologyList->technology_id = $request->input('technology_id');

        $technologyList->save();

        return Redirect::route('admin.technology-lists.index')
            ->with('success', 'TechnologyList updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $homeSlider = TechnologyList::find($id);
        if ($homeSlider->icon) {
            $previousIconPath = public_path($homeSlider->icon);
        
            if (file_exists($previousIconPath)) {
                unlink($previousIconPath); 
            }
        }
        TechnologyList::find($id)->delete();

        return Redirect::route('admin.technology-lists.index')
            ->with('success', 'TechnologyList deleted successfully');
    }
}
