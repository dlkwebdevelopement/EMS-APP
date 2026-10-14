<?php
// app/Http/Controllers/Admin/HolidayController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class HolidayController extends Controller
{
    public function index()
    {
        return view('admin.holidays.index');
    }
}
