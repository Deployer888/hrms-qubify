<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\Helper;
class TestController extends Controller
{
    public function test()
    {
        $data = Helper::clockinAttendance(5,'2025-02-12','10:45:00','09:30:00');
        // $data = Helper::clockoutAttendance(5,'2025-02-12','11:50:00');
        dd($data);
        // return view('test');
    }
}
