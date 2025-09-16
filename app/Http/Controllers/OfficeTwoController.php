<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Utility;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OfficeTwoController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('Manage Office'))
        {
            return view('officetwo.index');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}