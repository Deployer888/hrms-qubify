<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\{ServiceList, Service};
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ServiceListRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Auth;
class ServiceListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $serviceLists = ServiceList::paginate();

        return view('backend.service-list.index', compact('serviceLists'))
            ->with('i', ($request->input('page', 1) - 1) * $serviceLists->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $serviceList = new ServiceList();
        $types = Service::all();
        return view('backend.service-list.create', compact('serviceList','types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ServiceListRequest $request): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();

            $icon->move(public_path('images/serviceimages'), $iconFileName);

            $iconPath = 'images/serviceimages/' . $iconFileName;
            
        }
        ServiceList::create([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'service_id' => $request->input('service_id'),
            'icon' => $iconPath
        ]);

        return Redirect::route('admin.service-lists.index')
            ->with('success', 'ServiceList created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $serviceList = ServiceList::find($id);

        return view('backend.service-list.show', compact('serviceList'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $serviceList = ServiceList::find($id);
        $types = Service::all();
        return view('backend.service-list.edit', compact('serviceList','types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ServiceListRequest $request, ServiceList $serviceList): RedirectResponse
    {
        if ($request->hasFile('icon')) {
            $icon = $request->file('icon');
            $iconFileName = $icon->getClientOriginalName();
            $icon->move(public_path('images/serviceimages'), $iconFileName);
            if ($serviceList->icon) {
                $previousIconPath = public_path($serviceList->icon);
            
                if (file_exists($previousIconPath)) {
                    unlink($previousIconPath); // Delete previous icon file
                }
            }
            $serviceList->icon = 'images/serviceimages/' . $iconFileName;
            
        }

        $serviceList->name = $request->input('name');
        $serviceList->description = $request->input('description');
        $serviceList->service_id = $request->input('service_id');

        $serviceList->save();
        return Redirect::route('admin.service-lists.index')
            ->with('success', 'ServiceList updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $homeSlider = ServiceList::find($id);
        if ($homeSlider->icon) {
            $previousIconPath = public_path($homeSlider->icon);
        
            if (file_exists($previousIconPath)) {
                unlink($previousIconPath); 
            }
        }
        ServiceList::find($id)->delete();
        
        return Redirect::route('admin.service-lists.index')
            ->with('success', 'ServiceList deleted successfully');
    }
}
