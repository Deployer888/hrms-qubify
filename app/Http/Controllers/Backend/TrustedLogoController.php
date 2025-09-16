<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\TrustedLogo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TrustedLogoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TrustedLogoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $trustedLogos = TrustedLogo::paginate();

        return view('backend.trusted-logo.index', compact('trustedLogos'))
            ->with('i', ($request->input('page', 1) - 1) * $trustedLogos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $trustedLogo = new TrustedLogo();

        return view('backend.trusted-logo.create', compact('trustedLogo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TrustedLogoRequest $request): RedirectResponse
    {
        if ($request->hasFile('image')) {
            $icon = $request->file('image');
            $iconFileName = $icon->getClientOriginalName();

            $icon->move(public_path('site'), $iconFileName);

            $iconPath = 'site/' . $iconFileName;
            
        }
        TrustedLogo::create([
            'image' => $iconPath
        ]);

        return Redirect::route('admin.trusted-logos.index')
            ->with('success', 'TrustedLogo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $trustedLogo = TrustedLogo::find($id);

        return view('backend.trusted-logo.show', compact('trustedLogo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $trustedLogo = TrustedLogo::find($id);

        return view('backend.trusted-logo.edit', compact('trustedLogo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TrustedLogoRequest $request, TrustedLogo $trustedLogo): RedirectResponse
    {
        if ($request->hasFile('image')) {
            $icon = $request->file('image');
            $iconFileName = $icon->getClientOriginalName();
            $icon->move(public_path('site'), $iconFileName);
            if ($trustedLogo->image) {
                $previousIconPath = public_path($trustedLogo->image);
            
                if (file_exists($previousIconPath)) {
                    unlink($previousIconPath); // Delete previous icon file
                }
            }
            $trustedLogo->image = 'site/' . $iconFileName;
            $trustedLogo->save();
        }

        return Redirect::route('admin.trusted-logos.index')
            ->with('success', 'TrustedLogo updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $trustedLogo = TrustedLogo::find($id);
        if ($trustedLogo->image) {
            $previousIconPath = public_path($trustedLogo->image);
        
            if (file_exists($previousIconPath)) {
                unlink($previousIconPath); // Delete previous icon file
            }
        }
        TrustedLogo::find($id)->delete();

        return Redirect::route('admin.trusted-logos.index')
            ->with('success', 'TrustedLogo deleted successfully');
    }
}
