<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\{IndustryList,Industry};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\IndustryListRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Auth;
class IndustryListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $industryLists = IndustryList::paginate();

        return view('backend.industry-list.index', compact('industryLists'))
            ->with('i', ($request->input('page', 1) - 1) * $industryLists->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $industryList = new IndustryList();
        $types = Industry::all();
        return view('backend.industry-list.create', compact('types','industryList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IndustryListRequest $request): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();

            $icon->move(public_path('images/industryimages'), $iconFileName);

            $iconPath = 'images/industryimages/' . $iconFileName;
            
        }
        IndustryList::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'industry_id' => $request->input('industry_id'),
            'icon' => $iconPath
        ]);

        return Redirect::route('admin.industry-lists.index')
            ->with('success', 'IndustryList created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $industryList = IndustryList::find($id);

        return view('backend.industry-list.show', compact('industryList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $types = Industry::all();
        $industryList = IndustryList::find($id);
        return view('backend.industry-list.edit', compact('industryList','types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndustryListRequest $request, IndustryList $industryList): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            $icon->move(public_path('images/industryimages'), $iconFileName);
            if ($industryList->image) {
                $previousIconPath = public_path($homeSlider->image);
            
                if (file_exists($previousIconPath)) {
                    unlink($previousIconPath); // Delete previous icon file
                }
            }
            $industryList->icon = 'images/industryimages/' . $iconFileName;
            
        }

        $industryList->name = $request->input('name');
        $industryList->description = $request->input('description');
        $industryList->industry_id = $request->input('industry_id');

        $industryList->save();

        return Redirect::route('admin.industry-lists.index')
            ->with('success', 'IndustryList updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $homeSlider = IndustryList::find($id);
        if ($homeSlider->icon) {
            $previousIconPath = public_path($homeSlider->icon);
        
            if (file_exists($previousIconPath)) {
                unlink($previousIconPath); 
            }
        }
        IndustryList::find($id)->delete();

        return Redirect::route('admin.industry-lists.index')
            ->with('success', 'IndustryList deleted successfully');
    }
}
