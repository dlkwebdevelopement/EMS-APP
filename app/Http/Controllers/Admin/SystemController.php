<?php
// app/Http/Controllers/Admin/SystemController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class SystemController extends Controller
{
    public function info()
    {
        return view('admin.system.info');
    }
}
