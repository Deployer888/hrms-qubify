<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\IndustryRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Auth;
class IndustryController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $industries = Industry::paginate();

        return view('backend.industry.index', compact('industries'))
            ->with('i', ($request->input('page', 1) - 1) * $industries->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $industry = new Industry();

        return view('backend.industry.create', compact('industry'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(IndustryRequest $request): RedirectResponse
    {
        Industry::create($request->validated());

        return Redirect::route('admin.industries.index')
            ->with('success', 'Industry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $industry = Industry::find($id);

        return view('backend.industry.show', compact('industry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $industry = Industry::find($id);

        return view('backend.industry.edit', compact('industry'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(IndustryRequest $request, Industry $industry): RedirectResponse
    {
        $industry->update($request->validated());

        return Redirect::route('admin.industries.index')
            ->with('success', 'Industry updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Industry::find($id)->delete();

        return Redirect::route('admin.industries.index')
            ->with('success', 'Industry deleted successfully');
    }
}
