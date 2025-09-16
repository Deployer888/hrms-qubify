<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;
use App\Models\Technology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\TechnologyRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Auth;
class TechnologyController extends Controller
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
        $technologies = Technology::paginate();

        return view('backend.technology.index', compact('technologies'))
            ->with('i', ($request->input('page', 1) - 1) * $technologies->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $technology = new Technology();

        return view('backend.technology.create', compact('technology'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TechnologyRequest $request): RedirectResponse
    {
        Technology::create($request->validated());

        return Redirect::route('technologies.index')
            ->with('success', 'Technology created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $technology = Technology::find($id);

        return view('backend.technology.show', compact('technology'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $technology = Technology::find($id);

        return view('backend.technology.edit', compact('technology'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TechnologyRequest $request, Technology $technology): RedirectResponse
    {
        $technology->update($request->validated());

        return Redirect::route('technologies.index')
            ->with('success', 'Technology updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Technology::find($id)->delete();

        return Redirect::route('technologies.index')
            ->with('success', 'Technology deleted successfully');
    }
}
